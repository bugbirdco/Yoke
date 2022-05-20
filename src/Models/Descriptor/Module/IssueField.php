<?php

namespace BugbirdCo\Yoke\Models\Descriptor\Module;

use BugbirdCo\Yoke\Models\Descriptor\I18n;

/**
 * @property I18n $name
 * @property I18n $description
 */
abstract class IssueField extends Module
{
    public static function type(): string
    {
        return 'jiraIssueFields';
    }

    public static function location()
    {
        return null;
    }

    public abstract static function name(): I18n;
    public abstract static function description(): I18n;
//    public abstract static function type(): string;

}