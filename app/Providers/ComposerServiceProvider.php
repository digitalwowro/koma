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
         * Categories
         */
        view()->composer(
            [
                'layout',
                'ip-fields._form',
            ],
            'App\Composers\CategoryComposer@all'
        );

        view()->composer(
            'category.index', 'App\Composers\CategoryComposer@admin'
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

        /**
         * Groups
         */
        view()->composer(
            'groups.index', 'App\Composers\GroupComposer@admin'
        );

        view()->composer(
            'partials._share-modal', 'App\Composers\GroupComposer@shareModal'
        );

        view()->composer(
            'users._form', 'App\Composers\GroupComposer@keyValue'
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
