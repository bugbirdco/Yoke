<?php

namespace BugbirdCo\Yoke;

use BugbirdCo\Yoke\Components\Client\Provider as ClientProvider;
use BugbirdCo\Yoke\Components\Descriptor\Provider as DescriptorProvider;
use BugbirdCo\Yoke\Components\Lifecycle\Provider as LifecycleProvider;
use BugbirdCo\Yoke\Components\Framework\Provider as FrameworkProvider;
use Illuminate\Support\ServiceProvider;

class YokeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(LifecycleProvider::class);
        $this->app->register(DescriptorProvider::class);
        $this->app->register(ClientProvider::class);
        $this->app->register(FrameworkProvider::class);

        $this->app->singleton('yoke.yield_tenant', function () {
            switch (config('yoke.tenant.scheme')) {
                case 'auth':
                    return auth(config('yoke.tenant.guard'))->user();
            }

            throw new \Exception('Tenant could not be yielded'); // TODO: Change to dependency related exception
        });

        $this->app->singleton('yoke.yield_user', function () {
            switch (config('yoke.user.scheme')) {
                case 'user':
                    return auth(config('yoke.user.guard'))->user();
            }

            throw new \Exception('Tenant could not be yielded'); // TODO: Change to dependency related exception
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/yoke.php' => config_path('yoke.php')
        ], 'config');
        $this->publishes([
            __DIR__ . '/../app/Yoke' => app_path('Yoke')
        ], 'app');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
