<?php

namespace Laravel\Dusk\BrowserKit;

use Symfony\Component\DomCrawler\Form;
use Laravel\Dusk\BrowserKit\TestResponse;

trait SupportsBrowserKit
{
    /**
     * Visit the given URI with a GET request.
     *
     * @param  string  $uri
     * @return $this
     */
    public function visit($uri)
    {
        return $this->makeRequest('GET', $uri);
    }

    /**
     * Visit the given named route with a GET request.
     *
     * @param  string  $route
     * @param  array  $parameters
     * @return $this
     */
    public function visitRoute($route, $parameters = [])
    {
        return $this->makeRequest('GET', route($route, $parameters));
    }

    /**
     * Make a request to the application and create a Crawler instance.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $parameters
     * @param  array  $cookies
     * @param  array  $files
     * @return $this
     */
    protected function makeRequest($method, $uri, $parameters = [], $cookies = [], $files = [])
    {
        //todo: do we need this here?
        //$uri = $this->prepareUrlForRequest($uri);

        $response = $this->call($method, $uri, $parameters, $cookies, $files);

        $response = $this->followRedirects($response);

        $response = TestResponse::fromBaseResponse($response);

        $response->initialize($this);

        $response->assertPageLoaded($uri);

        return $response;
    }

    /**
     * Follow redirects from the last response.
     *
     * @return $this
     */
    protected function followRedirects($response)
    {
        while ($response->isRedirect()) {
            $response = $this->makeRequest('GET', $response->getTargetUrl());
        }

        return $response;
    }
}
