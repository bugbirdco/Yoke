<?php

namespace BugbirdCo\Yoke\Models\Descriptor\Module;

abstract class ProjectPageModule extends PageModule
{
    public static function type(): string
    {
        return 'jiraProjectPages';
    }
}