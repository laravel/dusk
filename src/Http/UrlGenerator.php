<?php

namespace Laravel\Dusk\Http;

use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use React\Http\Message\Uri;

/**
 * @method string query(string $path, array $query = [], mixed $extra = [], bool|null $secure = null)
 */
class UrlGenerator implements UrlGeneratorContract
{
    use ForwardsCalls;

    public function __construct(
        protected string $proxyHostname,
        protected int $proxyPort,
        protected string $appHost,
        protected UrlGeneratorContract $url,
    ) {
    }

    public function current()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function previous($fallback = false)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function to($path, $extra = [], $secure = null)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function secure($path, $parameters = [])
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function asset($path, $secure = null)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function route($name, $parameters = [], $absolute = true)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function signedRoute($name, $parameters = [], $expiration = null, $absolute = true)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function temporarySignedRoute($name, $expiration, $parameters = [], $absolute = true)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function action($action, $parameters = [], $absolute = true)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getRootControllerNamespace()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function setRootControllerNamespace($rootNamespace)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function proxy(string $url): string
    {
        // TODO: Provide a way to register a callback that allows for more complex matching

        $uri = new Uri($url);

        if ($uri->getHost() !== $this->appHost) {
            return $url;
        }

        $data = [$uri->getScheme(), $uri->getHost(), $uri->getPort()];
        $payload = urlencode(base64_encode(json_encode($data)));
        $query = $uri->getQuery().($uri->getQuery() ? '&' : '').'__dusk='.urlencode($payload);

        return (string) $uri
            ->withHost($this->proxyHostname)
            ->withPort($this->proxyPort)
            ->withQuery($query);
    }

    public function __call(string $name, array $arguments)
    {
        $result = $this->forwardDecoratedCallTo($this->url, $name, $arguments);

        if (is_string($result) && filter_var($result, FILTER_VALIDATE_URL)) {
            return $this->proxy($result);
        }

        return $result;
    }
}
