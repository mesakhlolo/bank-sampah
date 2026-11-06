<?php

$providers = [
    App\Providers\AppServiceProvider::class,
];

// Telescope lives in require-dev. Register its provider only when the package
// is actually installed, otherwise `composer install --no-dev` fatals at boot.
if (class_exists(Laravel\Telescope\TelescopeServiceProvider::class)) {
    $providers[] = App\Providers\TelescopeServiceProvider::class;
}

return $providers;
