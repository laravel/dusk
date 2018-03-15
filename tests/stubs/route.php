<?php

function route($name, $parameters = [], $absolute = true)
{
    $route = '/'.$name.'/'.implode('/', $parameters);

    if ($absolute) {
        $route = 'http://www.google.com'.$route;
    }

    return $route;
}
