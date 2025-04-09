<?php

namespace Laravel\Dusk\Driver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\HttpCommandExecutor;
use Facebook\WebDriver\Remote\WebDriverCommand;

class AsyncWebDriverFactory
{
    protected DesiredCapabilities $desiredCapabilities;

    protected DesiredCapabilities $sessionCapabilities;

    protected bool $isW3cCompliant;

    protected string $sessionId;

    /**
     * Construct a new factory instance.
     */
    public function __construct(
        protected string $seleniumServerUrl = 'http://localhost:9515',
        DesiredCapabilities|array|null $desiredCapabilities = null,
        protected ?int $connectionTimeoutMs = null,
        protected ?int $requestTimeoutMs = null,
        protected ?string $httpProxy = null,
        protected ?int $httpProxyPort = null,
        protected ?DesiredCapabilities $requiredCapabilities = null,
    ) {
        $this->seleniumServerUrl = rtrim($this->seleniumServerUrl, '/');

        $this->desiredCapabilities = match (true) {
            $desiredCapabilities instanceof DesiredCapabilities => $desiredCapabilities,
            is_array($desiredCapabilities) => new DesiredCapabilities($desiredCapabilities),
            default => new DesiredCapabilities(),
        };
    }

    /**
     * Create and initialize new AsyncWebDriver.
     *
     * @return AsyncWebDriver
     */
    public function __invoke(): AsyncWebDriver
    {
        $this->initializeSession();

        $executor = new AsyncCommandExecutor($this->seleniumServerUrl, $this->httpProxy, $this->httpProxyPort);

        $this->configureExecutor($executor);

        return new AsyncWebDriver($executor, $this->sessionId, $this->sessionCapabilities, $this->isW3cCompliant);
    }

    /**
     * Initialize the web driver session synchronously.
     *
     * @return void
     */
    protected function initializeSession(): void
    {
        $executor = $this->configureExecutor(
            new HttpCommandExecutor($this->seleniumServerUrl, $this->httpProxy, $this->httpProxyPort),
        );

        $response = $executor->execute(WebDriverCommand::newSession($this->parameters()));
        $value = $response->getValue();

        $this->isW3cCompliant = isset($value['capabilities']);

        $this->sessionCapabilities = $this->isW3cCompliant
            ? DesiredCapabilities::createFromW3cCapabilities($value['capabilities'])
            : new DesiredCapabilities($value['capabilities']);

        $this->sessionId = $response->getSessionID();
    }

    /**
     * Apply timeouts/configuration to the command executor.
     *
     * @param  HttpCommandExecutor  $executor
     * @return HttpCommandExecutor
     */
    protected function configureExecutor(HttpCommandExecutor $executor): HttpCommandExecutor
    {
        if (!is_null($this->connectionTimeoutMs)) {
            $executor->setConnectionTimeout($this->connectionTimeoutMs);
        }

        if (!is_null($this->requestTimeoutMs)) {
            $executor->setRequestTimeout($this->requestTimeoutMs);
        }

        return $executor;
    }

    /**
     * Convert desired/required capabilities into session parameters.
     *
     * @return array
     */
    protected function parameters(): array
    {
        // Set W3C parameters first
        $parameters = [
            'capabilities' => [
                'firstMatch' => [
                    (object) $this->desiredCapabilities->toW3cCompatibleArray(),
                ],
            ],
        ];

        // Handle *required* params
        if ($this->requiredCapabilities && count($this->requiredCapabilities->toArray())) {
            $parameters['capabilities']['alwaysMatch'] = (object) $this->requiredCapabilities->toW3cCompatibleArray();
            $this->desiredCapabilities->setCapability('requiredCapabilities',
                (object) $this->requiredCapabilities->toArray());
        }

        $parameters['desiredCapabilities'] = (object) $this->desiredCapabilities->toArray();

        return $parameters;
    }
}
