<?php

namespace BugbirdCo\Yoke\Components\Lifecycle;

use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('yoke.lifecycle-scheme', function () {
            return Scheme::make([
                'installed' => route('yoke.lifecycle.installed', [], false),
                'enabled' => route('yoke.lifecycle.enabled', [], false),
                'disabled' => route('yoke.lifecycle.disabled', [], false),
                'uninstalled' => route('yoke.lifecycle.uninstalled', [], false),
            ]);
        });
    }

    public function boot()
    {
        $this->app->make(Registrar::class)->group([
            'prefix' => '/yoke/lifecycle',
            'as' => 'yoke.',
            'middleware' => ['api']
        ], function (Registrar $router) {
            $router->post('/installed', Controller::class . '@installedEmitter')
                ->name('lifecycle.installed');

            $router->post('/enabled', Controller::class . '@enabledEmitter')
                ->name('lifecycle.enabled');

            $router->post('/disabled', Controller::class . '@disabledEmitter')
                ->name('lifecycle.disabled');

            $router->post('/uninstalled', Controller::class . '@uninstalledEmitter')
                ->name('lifecycle.uninstalled');
        });
    }
}
