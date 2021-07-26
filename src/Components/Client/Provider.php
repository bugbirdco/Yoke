<?php

namespace BugbirdCo\Yoke\Components\Client;

use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('yoke.client', function () {
            return new Client();
        });
    }
}
