<?php

namespace App\Providers;

use App\Services\Encryption;
use App\Services\PermissionSync;
use App\Services\Sharing;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('encrypt', function() {
            return new Encryption();
        });

        $this->app->singleton('share', function() {
            return new Sharing();
        });

        $this->app->singleton('permissionSync', function() {
            return new PermissionSync();
        });
    }
}
