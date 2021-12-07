<?php

namespace BugbirdCo\Yoke\Components\Framework;

use BugbirdCo\Yoke\Models\Auth\Tenant;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;

class TenantDriver implements UserProvider
{
    /**
     * The Eloquent user model.
     *
     * @var string
     */
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Get a new query builder for the model instance.
     *
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function newModelQuery($model = null)
    {
        return is_null($model)
            ? $this->createModel()->newQuery()
            : $model->newQuery();
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\' . ltrim($this->model, '\\');

        return new $class;
    }

    public function validateCredentials(UserContract $user, array $credentials)
    {
        try {
            JWT::decode($credentials['jwt'], $user->shared_secret, ['HS256']);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        return $this->newModelQuery($model)
            ->where($model->getAuthIdentifierName(), $identifier)
            ->first();
    }

    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();

        $retrievedModel = $this->newModelQuery($model)->where(
            $model->getAuthIdentifierName(), $identifier
        )->first();

        if (!$retrievedModel) {
            return null;
        }

        $rememberToken = $retrievedModel->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token)
            ? $retrievedModel : null;
    }

    /**
     * @param UserContract|Tenant $user
     * @param string $token
     */
    public function updateRememberToken(UserContract $user, $token)
    {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return null;
        }
        $query = $this->newModelQuery();

        $parts = explode('.', $credentials['jwt']);

        if (count($parts) !== 3) {
            return null;
        }

        $id = json_decode(base64_decode($parts[1]))->iss;

        return $query->find($id);
    }
}