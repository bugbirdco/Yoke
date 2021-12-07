<?php

namespace BugbirdCo\Yoke\Components\Framework;

use BugbirdCo\Yoke\Components\Framework\Actions\GetUser;
use BugbirdCo\Yoke\Facades\Descriptor;
use BugbirdCo\Yoke\Models\Auth\Tenant;
use BugbirdCo\Yoke\Models\Auth\User;
use Firebase\JWT\JWT;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;

class UserDriver implements UserProvider
{
    /**
     * The Eloquent user model.
     *
     * @var string|User
     */
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Create a new instance of the model.
     *
     * @return string|User
     */
    public function getModel()
    {
        $class = '\\' . ltrim($this->model, '\\');

        return $class;
    }


    /**
     * @param UserContract|User $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        try {
            /** @var Tenant $tenant */
            $tenant = app('yoke.yield_tenant');
            $body = JWT::decode($credentials['jwt'], $tenant->shared_secret, ['HS256']);
            return !empty($body->sub) && $user->account_id == $body->sub;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function retrieveById($identifier)
    {
        try {
            return $this->getModel()::make(Descriptor::act(new GetUser($identifier)));
        } catch (ClientException $e) {
            dd($e->getResponse());
        }
    }

    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    /**
     * @param UserContract|User $user
     * @param string $token
     */
    public function updateRememberToken(UserContract $user, $token)
    {
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) || empty($credentials['jwt'])) {
            return null;
        }

        $parts = explode('.', $credentials['jwt']);

        if (count($parts) !== 3) {
            return null;
        }

        $data = json_decode(base64_decode($parts[1]));

        if (empty($data->sub)) {
            return null;
        }

        return $this->getModel()::make(Descriptor::act(new GetUser($data->sub)));
    }
}