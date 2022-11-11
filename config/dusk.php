<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default chromedriver install path
    |--------------------------------------------------------------------------
    |
    | This is where the `dusk:chrome-driver` command will install the chromedriver
    | executable files. If none provided, this package's "bin" folder will be
    | used (normally this would be "vendor/laravel/dusk/bin/").
    |
    */

    'install-path' => env('CHROMEDRIVER_INSTALL_PATH'),
];
