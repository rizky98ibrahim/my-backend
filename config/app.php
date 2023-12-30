<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    // ! Application Name
    'name' => env('APP_NAME', 'Rizky Ibrahim'),

    // ! Application Environment
    'env' => env('APP_ENV', 'production'),

    // ! Application Debug Mode
    'debug' => (bool) env('APP_DEBUG', false),

    // ! Application URL
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),

    // ! Application Timezone
    'timezone' => 'Asia/Jakarta',

    // ! Application Locale Configuration
    'locale' => 'id',

    // ! Application Fallback Locale
    'fallback_locale' => 'en',

    // ! Faker Locale
    'faker_locale' => 'id_ID',

    // ! Encryption Key
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    // ! Maintenance Mode Driver
    'maintenance' => [
        'driver' => 'file',
        // 'store' => 'redis',
    ],

    // ! Autoloaded Service Providers
    'providers' => ServiceProvider::defaultProviders()->merge([
        // * Package Service Providers here...
        Spatie\Permission\PermissionServiceProvider::class,

        // * Application Service Providers...
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ])->toArray(),

    // ! Class Aliases
    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
    ])->toArray(),

];
