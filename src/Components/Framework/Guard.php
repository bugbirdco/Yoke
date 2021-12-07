<?php

namespace BugbirdCo\Yoke\Components\Framework;

use Illuminate\Auth\SessionGuard;

class Guard extends SessionGuard
{
    public function attempt(array $credentials = [], $remember = false)
    {
        return parent::attempt(
            [
                "jwt" => $credentials['jwt']
            ],
            $remember
        );
    }
}