<?php

namespace BugbirdCo\Yoke\Components\Descriptor;

use BugbirdCo\Yoke\Components\Framework\Authenticate;
use BugbirdCo\Yoke\Models\Descriptor\Descriptor;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    public function register()
    {
        Descriptor::register();
    }

    public function boot()
    {
        /** @var Registrar $routeRegistrar */
        $routeRegistrar = $this->app->make(Registrar::class);

        $routeRegistrar->get(
            '/yoke/atlassian-connect.json',
            YieldDescriptionController::class
        )
            ->name('yoke.descriptor');

        $routeRegistrar->get(
            '/yoke/handle/{content}',
            ContentHandlerController::class
        )
            ->middleware('web', Authenticate::class)
            ->name('yoke.handle-content');
    }
}
