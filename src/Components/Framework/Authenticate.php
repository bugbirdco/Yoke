<?php

namespace BugbirdCo\Yoke\Components\Framework;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        if ($jwt = $request->get('jwt', $request->has('authorization'))) {
            if ($this->auth->guard(config('yoke.tenant.guard'))->attempt(['jwt' => $jwt])) {
                $this->auth->guard(config('yoke.user.guard'))->attempt(['jwt' => $jwt]);
                return;
            }
        }
        parent::authenticate($request, $guards);
    }
}