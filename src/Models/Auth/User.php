<?php

namespace BugbirdCo\Yoke\Models\Auth;

use BugbirdCo\Cabinet\Model;
use BugbirdCo\Yoke\Models\Descriptor\Action\Action;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

/**
 * Class User
 * @package BugbirdCo\Yoke\Models\Auth
 *
 * @property string $self
 * @property string $account_id
 * @property string $account_type
 * @property string $name
 * @property string $email_address
 * @property string[] $avatar_urls
 * @property string $display_name
 * @property boolean $active
 * @property string $time_zone
 * @property string $locale
 * @property object $groups
 * @property object $application_roles
 * @property string $expand
 */
class User extends Model implements Authenticatable
{
    /**
     * @param array|Action $attrs
     * @return User
     * @throws \ReflectionException
     */
    public static function make($attrs = [])
    {
        if ($attrs instanceof Action)
            return parent::make(
                collect($attrs->getData())->reduce(function ($data, $val, $key) {
                    $data[Str::snake($key)] = $val;
                    return $data;
                }, [])
            );

        return parent::make($attrs);
    }

    public function getAuthIdentifierName()
    {
        return 'account_id';
    }

    public function getAuthIdentifier()
    {
        return $this->account_id;
    }

    public function getAuthPassword()
    {
        return null;
    }

    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
    }

    public function getRememberTokenName()
    {
        return null;
    }

    // Getters

    public function isRealUser()
    {
        return $this->account_type == 'atlassian';
    }

    public function isAppUser()
    {
        return $this->account_type == 'app';
    }

    public function isCustomerUser()
    {
        return $this->account_type == 'customer';
    }
}