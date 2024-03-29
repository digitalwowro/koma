<?php

namespace App\Providers;

use App\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        parent::registerPolicies();

        Gate::define('admin', function($user) {
            return $user->isAdmin();
        });

        Gate::define('view', function($user, $resource) {
            return Permission::can('view', $resource, $user);
        });

        Gate::define('edit', function($user, $resource) {
            return Permission::can('edit', $resource, $user);
        });

        Gate::define('delete', function($user, $resource) {
            return Permission::can('delete', $resource, $user);
        });

        Gate::define('create', function($user, $resource) {
            return Permission::can('create', $resource, $user);
        });

        Gate::define('share', function($user, $resource) {
            return $resource->isOwner($user); // @todo separate permission?
        });

        Gate::define('owner', function($user, $resource) {
            return $resource->isOwner($user);
        });

        Gate::define('manage', function($user, $resource) {
            return $resource->isOwner($user); // @todo separate permission?
        });
    }
}
