<?php

namespace BugbirdCo\Yoke;

use BugbirdCo\Yoke\Components\Client\Provider as ClientProvider;
use BugbirdCo\Yoke\Components\Descriptor\Provider as DescriptorProvider;
use BugbirdCo\Yoke\Components\Lifecycle\Provider as LifecycleProvider;
use Illuminate\Support\ServiceProvider;

class YokeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(LifecycleProvider::class);
        $this->app->register(DescriptorProvider::class);
        $this->app->register(ClientProvider::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/yoke.php' => config_path('yoke.php')
        ], 'config');
        $this->publishes([
            __DIR__ . '/../app/Yoke' => app_path('Yoke')
        ], 'app');
    }
}
