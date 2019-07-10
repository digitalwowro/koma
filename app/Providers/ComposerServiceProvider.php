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
            [
                'layout',
                'ip-fields._form',
                //'users._personalization',
            ],
            'App\Composers\DeviceSectionComposer@all'
        );

        view()->composer(
            'device-section.index', 'App\Composers\DeviceSectionComposer@admin'
        );

        /**
         * IP Categories
         */
        view()->composer(
            [
                'layout',
                'users._personalization',
            ], 'App\Composers\IpCategoryComposer@all'
        );

        view()->composer(
            'ip-category.index', 'App\Composers\IpCategoryComposer@admin'
        );

        /**
         * Users
         */
        view()->composer(
            'users.index', 'App\Composers\UserComposer@admin'
        );

        view()->composer(
            'partials._share-modal', 'App\Composers\UserComposer@shareModal'
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
