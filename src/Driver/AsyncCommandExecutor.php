<?php

namespace Laravel\Dusk\Driver;

use Facebook\WebDriver\Exception\Internal\LogicException;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Remote\HttpCommandExecutor;
use Facebook\WebDriver\Remote\WebDriverCommand;
use Facebook\WebDriver\Remote\WebDriverResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Loop;
use React\Http\Browser as ReactHttpClient;
use React\Http\Message\ResponseException;
use React\Promise\PromiseInterface;
use Throwable;

class AsyncCommandExecutor extends HttpCommandExecutor
{
    /**
     * Execute a web driver command asynchronously.
     *
     * @param  WebDriverCommand  $command
     * @return WebDriverResponse
     */
    public function execute(WebDriverCommand $command): WebDriverResponse
    {
        $client = (new ReactHttpClient())->withRejectErrorResponse(false);

        [$url, $method, $headers, $payload] = $this->extractRequestDataFromCommand($command);

        return $this->sendRequestAndWaitForResponse(match ($method) {
            'GET' => $client->get($url, $headers),
            'POST' => $client->post($url, $headers, $this->encodePayload($payload)),
            'DELETE' => $client->delete($url, $headers),
        });
    }

    /**
     * Run event loop until request is fulfilled.
     *
     * @param  PromiseInterface  $request
     * @return WebDriverResponse
     *
     * @throws JsonException
     * @throws WebDriverException
     */
    protected function sendRequestAndWaitForResponse(PromiseInterface $request): WebDriverResponse
    {
        $resolved = null;

        $request->then(function ($response) use (&$resolved) {
            Loop::get()->futureTick(fn() => Loop::stop());
            $resolved = $response;
        });

        $request->catch(function (Throwable $exception) use (&$resolved) {
            if ($resolved instanceof ResponseException) {
                $resolved = $exception->getResponse();
            } else {
                throw $exception;
            }
        });

        while ($resolved === null) {
            Loop::run();
        }

        return $this->mapAsyncResponseToWebDriverResponse($resolved);
    }

    /**
     * Parse HTTP response and map to web driver response.
     *
     * @param  ResponseInterface  $response
     * @return WebDriverResponse
     *
     * @throws JsonException
     * @throws WebDriverException
     */
    protected function mapAsyncResponseToWebDriverResponse(ResponseInterface $response): WebDriverResponse
    {
        $value = null;
        $message = null;
        $sessionId = null;
        $status = 0;

        $results = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if (is_array($results)) {
            $value = Arr::get($results, 'value');
            $message = Arr::get($results, 'message');
            $status = Arr::get($results, 'status', 0);

            if (is_array($value) && array_key_exists('sessionId', $value)) {
                $sessionId = $value['sessionId'];
            } elseif (array_key_exists('sessionId', $results)) {
                $sessionId = $results['sessionId'];
            }
        }

        if (is_array($value) && isset($value['error'])) {
            WebDriverException::throwException($value['error'], $message, $results);
        }

        if ($status !== 0) {
            WebDriverException::throwException($status, $message, $results);
        }

        return (new WebDriverResponse($sessionId))->setStatus($status)->setValue($value);
    }

    /**
     * Ensure that payload is always a JSON object.
     *
     * @param  Collection  $payload
     * @return string
     */
    protected function encodePayload(Collection $payload): string
    {
        // POST body must be valid JSON object, even if empty: https://www.w3.org/TR/webdriver/#processing-model
        if ($payload->isEmpty()) {
            return '{}';
        }

        return $payload->toJson();
    }

    /**
     * Extract data necessary to make HTTP request for web driver command.
     *
     * @param  WebDriverCommand  $command
     * @return array{0: string, 1: string, 2: array, 3: Collection}
     *
     * @throws LogicException
     */
    protected function extractRequestDataFromCommand(WebDriverCommand $command): array
    {
        ['url' => $path, 'method' => $method] = $this->getCommandHttpOptions($command);

        // Keys that are prefixed with ":" are URL parameters. All others are JSON payload data.
        [$parameters, $payload] = collect($command->getParameters() ?? [])
            ->put(':sessionId', (string) $command->getSessionID())
            ->partition(fn($value, $key) => str_starts_with($key, ':'));

        if ($payload->isNotEmpty() && $method !== 'POST') {
            throw LogicException::forInvalidHttpMethod($path, $method, $payload->all());
        }

        $url = $this->url.$this->applyParametersToPath($parameters, $path);
        $method = strtoupper($method);
        $headers = $this->defaultHeaders($method);

        return [$url, $method, $headers, $payload];
    }

    /**
     * Replace prefixed placeholders with request parameters.
     *
     * @param  Collection  $parameters
     * @param  string  $path
     * @return string
     */
    protected function applyParametersToPath(Collection $parameters, string $path): string
    {
        return str_replace($parameters->keys()->all(), $parameters->values()->all(), $path);
    }

    /**
     * Get the default HTTP headers for a given request method.
     *
     * @param  string  $method
     * @return array
     */
    protected function defaultHeaders(string $method): array
    {
        $headers = collect(static::DEFAULT_HTTP_HEADERS)->mapWithKeys(function ($header) {
            [$key, $value] = explode(':', $header, 2);
            return [$key => $value];
        });

        if (in_array($method, ['POST', 'PUT'], true)) {
            $headers->put('Expect', '');
        }

        return $headers->all();
    }
}
