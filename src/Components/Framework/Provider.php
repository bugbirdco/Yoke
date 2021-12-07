<?php

namespace BugbirdCo\Yoke\Components\Framework;

use BugbirdCo\Yoke\Components\Lifecycle\InstalledEvent;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Support\Facades\Auth;

class Provider extends EventServiceProvider
{
    protected $listen = [
        InstalledEvent::class => [
            CreateTenant::class
        ]
    ];

    public function register()
    {
        parent::register();

        Auth::extend('yoke', function (Application $app, $name, array $config) {
            return new Guard(
                $name,
                Auth::createUserProvider($config['provider']),
                $app['session.store'],
                $app['request']
            );
        });

        Auth::provider('yoke-tenant', function (Application $app, array $config) {
            return new TenantDriver($config['model']);
        });

        Auth::provider('yoke-user', function (Application $app, array $config) {
            return new UserDriver($config['model']);
        });
    }
}
