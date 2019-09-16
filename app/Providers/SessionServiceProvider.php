<?php

namespace App\Providers;

use App\KomaSession;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\ServiceProvider;

class SessionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(ConnectionInterface $connection)
    {
        \Session::extend('koma-database', function($app) use ($connection) {
            $table = config('session.table');
            $minutes = config('session.lifetime');

            return new KomaSession($connection, $table, $minutes);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
