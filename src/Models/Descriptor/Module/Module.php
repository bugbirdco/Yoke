<?php

namespace BugbirdCo\Yoke\Models\Descriptor\Module;

use BugbirdCo\Cabinet\Model;

/**
 * Class Module
 * @package BugbirdCo\Yoke\Models\Descriptor\Module
 *
 * @property string $type
 * @property array $scopes
 * @property boolean|null $available
 */
abstract class Module extends Model
{
    public static function make($attrs)
    {
        return parent::make($attrs + [
                'scopes' => static::scopes(),
                'available' => static::available()
            ]);
    }

    abstract public static function scopes(): array;

    public static function available(): ?bool
    {
        return null;
    }
}
