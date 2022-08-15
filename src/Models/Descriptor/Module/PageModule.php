<?php

namespace BugbirdCo\Yoke\Models\Descriptor\Module;

use BugbirdCo\Yoke\Models\Descriptor\I18n;

/**
 * @property string $url
 * @property I18n $name
 * @property boolean $cacheable
 */
abstract class PageModule extends Module
{
    public static function type(): string
    {
        return 'generalPages';
    }

    public static function location()
    {
        return null;
    }

    public abstract static function content(): string|callable|object;

    public abstract static function name(): I18n|array|string;

    public static function make($attrs = []): Module
    {
        $content = static::content();
        return parent::make($attrs + [
                'url' => is_string($content) ? $content : route('yoke.handle-content', ['content' => static::getKey()], false),
                'name' => static::name(),
                'cacheable' => false,
            ]);
    }
}
