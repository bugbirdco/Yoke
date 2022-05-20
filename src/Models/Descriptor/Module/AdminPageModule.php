<?php

namespace BugbirdCo\Yoke\Models\Descriptor\Module;

abstract class AdminPageModule extends PageModule
{
    public static function type(): string
    {
        return 'adminPages';
    }
}