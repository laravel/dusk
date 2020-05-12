<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dusk Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Laravel dusk will locate its internal API routes.
    |
    */

    'path' => '_dusk',

    /*
    |--------------------------------------------------------------------------
    | Dusk Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Dusk will access its internal API routes. If this
    | setting is null, Dusk will reside under the same domain as the
    | application. Otherwise, this value will serve as the subdomain.
    |
    */

    'domain' => null,
];
