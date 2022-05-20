<?php

namespace BugbirdCo\Yoke\Models\Descriptor\Module;

/**
 * @property string $url
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

    public static function make($attrs = []): Module
    {
        $content = static::content();
        return parent::make($attrs + [
            'cacheable' => false,
            'url' => is_string($content) ? $content : route('yoke.handle-content', ['content' => static::getKey()], false),
        ]);
    }
}