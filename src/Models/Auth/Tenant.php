<?php

namespace BugbirdCo\Yoke\Models\Auth;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Tenant
 * @package BugbirdCo\Yoke\Models\Eloquent
 *
 * @property string $client_key
 * @property string $key
 * @property string $shared_secret
 * @property string $base_url
 * @property string $display_url
 * @property string $display_url_servicedesk_help_center
 * @property string $product_type
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $disabled_at
 * @property Carbon|null $uninstalled_at
 *
 */
class Tenant extends Model implements Authenticatable
{
    public $incrementing = false;
    protected $primaryKey = 'client_key';

    protected $fillable = [
        'client_key',
        'key',
        'shared_secret',
        'base_url',
        'display_url',
        'display_url_servicedesk_help_center',
        'product_type',
        'description',
    ];

    protected $casts = [
        'shared_secret' => 'encrypted'
    ];

    public static function primaryKey()
    {
        return (new static)->getKeyName();
    }

    public static function fillables()
    {
        return (new static)->getFillable();
    }

    // Auth related stuff

    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
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

    public function isJira()
    {
        return $this->product_type === 'jira';
    }

    public function isConfluence()
    {
        return $this->product_type === 'confluence';
    }

    public function isInstalled()
    {
        return !$this->isUninstalled();
    }

    public function isUninstalled()
    {
        return !empty($this->uninstalled_at);
    }

    public function isEnabled()
    {
        return !$this->isDisabled();
    }

    public function isDisabled()
    {
        return !empty($this->disabled_at);
    }

    public function displayUrl()
    {
        return $this->display_url ?? $this->base_url;
    }

    public function serviceDeskDisplayUrl()
    {
        return $this->display_url_servicedesk_help_center ?? $this->displayUrl();
    }
}