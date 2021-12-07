<?php

namespace BugbirdCo\Yoke\Models\Descriptor\Module;

use BugbirdCo\Cabinet\Model;
use Illuminate\Support\Str;

/**
 * Class Module
 * @package BugbirdCo\Yoke\Models\Descriptor\Module
 *
 * @property string $type
 * @property array $scopes
 * @property string $location
 * @property string $key
 * @property boolean|null $available
 */
abstract class Module extends Model
{
    protected static $isKeyed = true;
    protected static $isRoot = false;

    protected static $namespace = 'App\\Yoke\\Modules\\';

    public $exclude = ['type'];

    protected static function getKey()
    {
        return Str::snake(
            str_replace(
                '\\',
                '-',
                str_replace(
                    static::$namespace,
                    '',
                    static::class
                )),
            '-'
        );
    }

    /**
     * @param null|static $location
     * @return Module|string|static
     */
    protected static function getLocation($location = null)
    {
        /** @noinspection Annotator */
        /** @var static $location */
        $location = $location ?? static::location();

        if (is_string($location) && str_contains($location, '\\')) {
            if ($location::$isRoot) return $location::getKey();
            else {
                $parent = $location::location();
                return (str_contains($parent, '\\') ? $parent::getKey() : $parent) . '/' . $location::getKey();
            }
        }

        return $location;
    }

    public static function make($attrs = []): Module
    {
        /** @noinspection Annotator */
        return parent::make($attrs + [
                'type' => static::type(),
                'scopes' => static::scopes(),
                'location' => static::getLocation(),
                'available' => static::available(),
            ] + (static::$isKeyed ? ['key' => static::getKey()] : []));
    }

    abstract public static function type(): string;

    /**
     * @return string|static
     */
    abstract public static function location();

    abstract public static function scopes(): array;

    public static function available(): ?bool
    {
        return null;
    }

    public function jsonSerialize()
    {
        return array_filter(parent::jsonSerialize()->jsonSerialize(), function ($val) {
            return !(empty($val) && $val !== 0);
        });
    }
}
