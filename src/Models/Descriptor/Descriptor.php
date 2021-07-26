<?php

namespace BugbirdCo\Yoke\Models\Descriptor;

use BugbirdCo\Cabinet\Deferrer\DeferresAccess;
use BugbirdCo\Cabinet\Model;
use BugbirdCo\Yoke\Components\Client\Client;
use BugbirdCo\Yoke\Models\Descriptor\Action\Action;
use BugbirdCo\Yoke\Models\Descriptor\Module\Module;
use BugbirdCo\Yoke\Components\Lifecycle\Scheme;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

/**
 * Class Descriptor
 * @package BugbirdCo\Yoke\Models
 *
 * @property Module[] $modules
 * @property Action[] $actions
 * @property string $key
 * @property string $aliasKey
 * @property string $name
 * @property string $description
 * @property Vendor $vendor
 * @property Links $links
 * @property boolean $enableLicensing
 * @property string[] $scopes
 * @property object|null $translations
 * @property object|null $apiMigrations
 * @property object|null $regionBaseUrls
 * @property integer|null $apiVersion
 * @property string|null $version
 */
abstract class Descriptor extends Model
{
    public static function register()
    {
        app()->singleton('yoke.descriptor', function () {
            /** @var Descriptor $descriptor */
            $descriptor = config('yoke.descriptor');
            if (is_callable($descriptor))
                $descriptor = app()->call($descriptor);
            else
                $descriptor = $descriptor::make();

            return $descriptor;
        });
    }

    public static function make($attrs = [])
    {
        return parent::make($attrs + [
                'modules' => static::modules(),
                'actions' => static::actions(),
                'key' => static::key(),
                'aliasKey' => static::aliasKey(),
                'name' => static::name(),
                'description' => static::description(),
                'vendor' => static::vendor(),
                'links' => static::links(),
                'enableLicensing' => static::isLicensable(),
                'scopes' => static::scopes(),
                'translations' => static::translations(),
                'apiMigrations' => static::apiMigrations(),
                'regionBaseUrls' => static::regionBaseUrls(),
                'apiVersion' => static::apiVersion(),
                'version' => static::version(),
            ]);
    }

    public static function key(): string
    {
        return str_replace(' ', '_', strtolower(config('app.name')));
    }

    public static function aliasKey(): ?int
    {
        return null;
    }

    abstract public static function description(): ?string;

    public static function isLicensable(): ?bool
    {
        return false;
    }

    public static function links(): ?Links
    {
        return null;
    }

    /**
     * @return Module[]
     */
    public static function modules(): array
    {
        return [];
    }

    /**
     * @return Action[]
     */
    public static function actions(): array
    {
        return [];
    }

    abstract public static function name(): string;

    /**
     * @return string[]
     */
    public static function scopes(): array
    {
        return [];
    }

    public static function translations(): ?object
    {
        return null;
    }

    public static function apiMigrations(): ?object
    {
        return null;
    }

    public static function regionBaseUrls(): ?object
    {
        return null;
    }

    public static function vendor(): ?Vendor
    {
        return null;
    }

    public static function version(): ?int
    {
        return null;
    }

    public static function apiVersion(): ?int
    {
        return null;
    }

    /**
     * @param string $action
     * @param ...$arguments
     * @return Action|object<$a>
     */
    public function act($action, ...$arguments)
    {
        /** @var Action $action */
        /** @var Client $client */
        $client = app('yoke.client');
        $client->setPlaceholderArgs($arguments);
        return new $action($action::request($client, $arguments));
    }

    public function jsonSerialize()
    {
        /** @var Scheme $lifecycleScheme */
        $lifecycleScheme = app('yoke.lifecycle-scheme');
        return [
                'authentication' => ['type' => 'JWT'],
                'baseUrl' => App::environment('production') ? config('app.url') : secure_url('/'),
                'lifecycle' => $lifecycleScheme,
                'modules' => (object)$this->getStaticModules()->groupBy('type')->toArray(),
                'scope' => (object)$this->getScopes(),
            ] + array_filter(
                $this->attributes->raw(null, ['modules', 'actions', 'scopes']),
                function ($item) {
                    return !(empty($item) || (is_object($item) && $item instanceof DeferresAccess));
                }
            );
    }

    /** @var Module[]|Collection */
    protected $_modules;

    /** @return Module[]|Collection */
    protected function getModules()
    {
        return empty($this->_modules) ? ($this->_modules = collect($this->modules)) : $this->_modules;
    }

    protected function getStaticModules()
    {
        return $this->getModules()->where('available', '===', null);
    }

    protected function getDynamicModules()
    {
        return $this->getModules()->where('available', '!==', null);
    }

    /** @var string[]|Collection */
    protected $_scopes;
    /** @var Action[]|Collection */
    protected $_actions;

    protected function getScopes()
    {
        if (empty($this->_scopes))
            $this->_scopes = collect($this->scopes);
        if (empty($this->_actions))
            $this->_actions = collect($this->actions);

        return $this->_scopes
            ->concat($this->getModules()->pluck('scopes')->flatten())
            ->concat($this->_actions->map(function (Action $action) {
                return $action::scopes();
            })->flatten())
            ->unique();
    }
}
