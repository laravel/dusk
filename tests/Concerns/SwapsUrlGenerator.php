<?php

namespace Laravel\Dusk\Tests\Concerns;

use Illuminate\Container\Container;

trait SwapsUrlGenerator
{
    protected function swapUrlGenerator()
    {
        Container::getInstance()->bind('url', function () {
            return new class {
                public function route($name, $parameters = [], $absolute = true)
                {
                    $route = '/'.$name.'/'.implode('/', $parameters);

                    if ($absolute) {
                        $route = 'http://www.google.com'.$route;
                    }

                    return $route;
                }
            };
        });
    }

    protected function resetContainer()
    {
        Container::setInstance();
    }
}
