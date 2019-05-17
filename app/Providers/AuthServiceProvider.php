<?php

namespace App\Providers;

use App\DeviceSection;
use App\Permission;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        parent::registerPolicies($gate);

        $gate->define('superadmin', function($user) {
            return $user->isSuperAdmin();
        });

        $gate->define('admin', function($user) {
            return $user->isAdmin();
        });

        $gate->define('list', function($user, DeviceSection $section) {
            return $user->isAdmin() || Permission::canList($section);
        });

        $gate->define('view', function($user, $resource) {
            return $user->isAdmin() || Permission::can('view', $resource);
        });

        $gate->define('edit', function($user, $resource) {
            return $user->isAdmin() || Permission::can('edit', $resource);
        });

        $gate->define('delete', function($user, $resource) {
            return $user->isAdmin() || Permission::can('delete', $resource);
        });

        $gate->define('create', function($user, DeviceSection $resource) {
            return $user->isAdmin() || Permission::can('create', $resource);
        });

        $gate->define('manage', function($user, DeviceSection $resource) {
            return $user->isAdmin() || Permission::can('manage', $resource);
        });
    }
}
