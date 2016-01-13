<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register view composers for your application.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Device Sections
         */
        view()->composer(
            'layout', 'App\Composers\DeviceSectionComposer@all'
        );

        view()->composer(
            'device-sections.index', 'App\Composers\DeviceSectionComposer@admin'
        );

        /**
         * Users
         */
        view()->composer(
            'users.index', 'App\Composers\UserComposer@admin'
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}
