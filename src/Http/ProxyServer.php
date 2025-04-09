<?php

namespace Laravel\Dusk\Http;

use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use React\Http\HttpServer as ReactHttpServer;
use React\Http\Message\Response;
use React\Http\Middleware\LimitConcurrentRequestsMiddleware;
use React\Http\Middleware\RequestBodyBufferMiddleware;
use React\Http\Middleware\RequestBodyParserMiddleware;
use React\Http\Middleware\StreamingRequestMiddleware;
use React\Promise\Promise;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;
use React\Stream\ReadableResourceStream;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\MimeTypes;
use Throwable;

class ProxyServer
{
    /**
     * The ReactPHP socket server instance.
     *
     * @var SocketServer
     */
    protected SocketServer $socket;

    /**
     * A count of active requests pending a response.
     *
     * @var int
     */
    protected int $requestsInFlight = 0;

    /**
     * All open TCP connections.
     *
     * @var ConnectionInterface[]
     */
    protected array $connections = [];

    /**
     * Whether the server is flushing active connections.
     *
     * @var bool
     */
    protected bool $flushing = false;

    /**
     * Proxy server constructor.
     *
     * @param  HttpKernel  $kernel
     * @param  LoopInterface  $loop
     * @param  HttpFoundationFactory  $factory
     * @param  string  $host
     * @param  int  $port
     */
    public function __construct(
        protected HttpKernel $kernel,
        protected LoopInterface $loop,
        protected HttpFoundationFactory $factory,
        public readonly string $host = '127.0.0.1',
        public readonly int $port = 8099,
    ) {
    }

    /**
     * Start listening for incoming HTTP connections, and pass them thru the Kernel.
     *
     * @return $this
     */
    public function listen(): static
    {
        if (!isset($this->socket)) {
            $this->socket = new SocketServer("{$this->host}:{$this->port}", [], $this->loop);

            $this->socket->on('connection', function (ConnectionInterface $connection) {
                $this->connections[] = $connection;
            });

            $server = new ReactHttpServer(
                $this->loop,
                new StreamingRequestMiddleware(),
                new LimitConcurrentRequestsMiddleware(100),
                new RequestBodyBufferMiddleware(32 * 1024 * 1024), // 32 MB
                new RequestBodyParserMiddleware(32 * 1024 * 1024, 100), // 32 MB
                $this->handleRequest(...),
            );

            $server->listen($this->socket);
        }

        return $this;
    }

    /**
     * Handle the request.
     *
     * @param  ServerRequestInterface  $request
     * @return Promise|Response
     */
    protected function handleRequest(ServerRequestInterface $request): Promise|Response
    {
        $request = $this->rewriteRequestUri($request);

        // If this is just a request for a static asset, just stream that content back
        if ($static_response = $this->staticResponse($request)) {
            return $static_response;
        }

        $promise = $this->runRequestThroughKernel(
            Request::createFromBase($this->factory->createRequest($request)),
        );

        // Handle exception
        $promise->catch(function (Throwable $exception) {
            return Response::plaintext($exception->getMessage()."\n".$exception->getTraceAsString())
                ->withStatus(Response::STATUS_INTERNAL_SERVER_ERROR);
        });

        return $promise;
    }

    protected function rewriteRequestUri(ServerRequestInterface $request): ServerRequestInterface
    {
        $uri = $request->getUri();

        $params = $request->getQueryParams();

        [$scheme, $host, $port] = json_decode(base64_decode($params['__dusk']));

        unset($params['__dusk']);

        return $request
            ->withUri($uri->withScheme($scheme)->withHost($host)->withPort($port)->withQuery(http_build_query($params)))
            ->withQueryParams($params);
    }

    /**
     * Pass a dynamic request to the Kernel.
     *
     * @param  Request  $request
     * @return Promise
     */
    protected function runRequestThroughKernel(Request $request): Promise
    {
        $this->requestsInFlight++;

        return new Promise(function (callable $resolve) use ($request) {
            $this->loop->futureTick(fn() => $this->loop->stop());

            $response = $this->kernel->handle($request);

            $resolve(new Response(
                status: $response->getStatusCode(),
                headers: $this->normalizeResponseHeaders($response->headers),
                body: $this->getResponseContent($response),
                version: $response->getProtocolVersion(),
            ));

            $this->kernel->terminate($request, $response);

            $this->requestsInFlight--;
        });
    }

    /**
     * Extract the content from a Symfony response for async use.
     *
     * @param  SymfonyResponse  $response
     * @return string
     */
    protected function getResponseContent(SymfonyResponse $response): string
    {
        ob_start();

        $response->sendContent();

        return ob_get_clean();
    }

    /**
     * Normalize Symfony headers for async use.
     *
     * @param  ResponseHeaderBag  $bag
     * @return array
     */
    protected function normalizeResponseHeaders(ResponseHeaderBag $bag): array
    {
        $headers = $bag->all();

        if (!empty($cookies = $bag->getCookies())) {
            $headers['Set-Cookie'] = [];
            foreach ($cookies as $cookie) {
                $headers['Set-Cookie'][] = (string) $cookie;
            }
        }

        return $headers;
    }

    /**
     * Return a static response if the request is for a public asset.
     *
     * @param  ServerRequestInterface  $request
     * @return Promise|null
     */
    protected function staticResponse(ServerRequestInterface $request): ?Promise
    {
        $path = $request->getUri()->getPath();

        if (Str::contains($path, '../')) {
            return null;
        }

        $filepath = public_path($path);

        if (file_exists($filepath) && !is_dir($filepath)) {
            $this->requestsInFlight++;

            return new Promise(function (callable $resolve) use ($filepath) {
                $resolve(new Response(status: 200, headers: [
                    'Content-Type' => match (pathinfo($filepath, PATHINFO_EXTENSION)) {
                        'css' => 'text/css',
                        'js' => 'application/javascript',
                        'png' => 'image/png',
                        'jpg', 'jpeg' => 'image/jpeg',
                        'svg' => 'image/svg+xml',
                        'woff' => 'font/woff',
                        'woff2' => 'font/woff2',
                        'eot' => 'application/vnd.ms-fontobject',
                        'ttf' => 'font/ttf',
                        default => (new MimeTypes())->guessMimeType($filepath),
                    },
                ], body: new ReadableResourceStream(fopen($filepath, 'r'))));

                $this->requestsInFlight--;
            });
        }

        return null;
    }

    /**
     * Flush pending requests and close all connections.
     *
     * @return void
     */
    public function flush(): void
    {
        if ($this->flushing) {
            return;
        }

        $this->flushing = true;

        $this->loop->addPeriodicTimer(0.1, function (TimerInterface $timer) {
            if ($this->requestsInFlight === 0) {
                foreach ($this->connections as $connection) {
                    $connection->close();
                }
                $this->connections = [];
                $this->socket->close();
                $this->loop->cancelTimer($timer);
            }
        });

        $this->loop->run();
    }

    /**
     * Ensure that connections are flushed when server is destroyed.
     */
    public function __destruct()
    {
        $this->flush();
    }
}
