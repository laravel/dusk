<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dusk driver path
    |--------------------------------------------------------------------------
    |
    | This value holds the path where the chrome's driver will be installed
    | when the command dusk:chrome-driver is run.
    | You typically don't need to change this path, but there may be some
    | situation when you need to change it, for instance if you build a phar
    | standalone app.
    |
    */

    'driver_path' => env('DUSK_DRIVER_PATH', __DIR__.'/../bin'),

];
