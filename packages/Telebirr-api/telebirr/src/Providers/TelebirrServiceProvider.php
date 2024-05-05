<?php

namespace Dagim\TelebirrApi\Providers;

use Illuminate\Support\ServiceProvider;
use Dagim\TelebirrApi\Telebirr;

class TelebirrServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // ...
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/telebirr.php', 'telebirr');

        $this->app->bind(Telebirr::class, function ($app) {
            return new Telebirr(
                config('telebirr.app_id'),
                config('telebirr.app_key'),
                config('telebirr.public_key'),
                 config('telebirr.private_key'),
                config('telebirr.api'),
                config('telebirr.short_code'),
                config('telebirr.notify_url'),
                config('telebirr.return_url'),
                config('telebirr.timeout_express'),
                config('telebirr.receive_name')
            );
        });
    }
}
