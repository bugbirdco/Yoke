<?php

namespace BugbirdCo\Yoke\Components\Descriptor;

use App\Yoke\Actions\GetUser;
use BugbirdCo\Yoke\Models\Descriptor\Descriptor;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    public function register()
    {
        Descriptor::register();

        /** @var Descriptor $a */
        $a->abcd(GetUser::class)->zzz();
    }

    public function boot()
    {
        $this->app->make(Registrar::class)->get(
            '/yoke/atlassian-connect.json',
            function () {
                return response()->json(app('yoke.descriptor'));
            }
        )->name('yoke.descriptor');
    }
}
