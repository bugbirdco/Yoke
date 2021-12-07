<?php

namespace BugbirdCo\Yoke\Models\Descriptor;

use BugbirdCo\Cabinet\Deferrer\DeferresAccess;
use BugbirdCo\Cabinet\Model;
use BugbirdCo\Yoke\Components\Client\Client;
use BugbirdCo\Yoke\Components\Framework\Actions\GetMyself;
use BugbirdCo\Yoke\Models\Descriptor\Action\Action;
use BugbirdCo\Yoke\Models\Descriptor\Module\Module;
use BugbirdCo\Yoke\Components\Lifecycle\Scheme;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

/**
 * Class Descriptor
 * @package BugbirdCo\Yoke\Models
 *
 * @property Module[] $modules
 * @property string[] $actions
 * @property string $key
 * @property string $aliasKey
 * @property string $name
 * @property string $description
 * @property Vendor $vendor
 * @property Links $links
 * @property boolean $enable_licensing
 * @property string[] $scopes
 * @property object|null $translations
 * @property object|null $api_migrations
 * @property object|null $region_base_urls
 * @property integer|null $api_version
 * @property string|null $version
 */
abstract class Descriptor extends Model
{
    protected static $internalActions = [
        GetMyself::class
    ];

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
                'modules' => collect(static::modules())
                    ->map(function ($module) {
                        /** @var Module $module */
                        return $module::make();
                    })->toArray(),
                'actions' => collect(static::actions())
                    ->concat(static::$internalActions)->toArray(),
                'key' => static::key(),
                'aliasKey' => static::aliasKey(),
                'name' => static::name(),
                'description' => static::description(),
                'vendor' => static::vendor(),
                'links' => static::links(),
                'enable_licensing' => static::isLicensable(),
                'scopes' => static::scopes(),
                'translations' => static::translations(),
                'api_migrations' => static::apiMigrations(),
                'region_base_urls' => static::regionBaseUrls(),
                'api_version' => static::apiVersion(),
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
     * @return Action
     */
    public function act($action)
    {
        /** @var Action $action */
        /** @var Client $client */
        $client = app('yoke.client');
        $client->setPlaceholderArgs($action->getArgs());
        if ($action->getTenant()) $client->setTenant($action->getTenant());
        return $action->hydrate($action::request($client, $action->getArgs()));
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
                'scopes' => $this->getScopes(),
            ] + collect($this->attributes->raw(null, ['modules', 'actions', 'scopes']))
                ->filter(function ($item) {
                    return !(empty($item) || (is_object($item) && $item instanceof DeferresAccess));
                })
                ->reduce(function ($data, $val, $key) {
                    $data[Str::camel($key)] = $val;
                    return $data;
                }, []);
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
            ->concat($this->_actions->map(function ($action) {
                /** @var Action $action */
                return $action::scopes();
            })->flatten())
            ->unique()
            ->toArray();
    }
}
