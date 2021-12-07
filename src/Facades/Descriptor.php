<?php

namespace BugbirdCo\Yoke\Facades;

use BugbirdCo\Yoke\Models\Descriptor\Action\Action;
use BugbirdCo\Yoke\Models\Descriptor\Descriptor as RealDescriptor;
use Illuminate\Support\Facades\Facade;

/**
 * Class Descriptor
 * @package BugbirdCo\Yoke
 *
 * @method static void register()
 * @method static Action act(string|Action $action, ...$arguments)
 *
 * @see RealDescriptor
 */
class Descriptor extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'yoke.descriptor';
    }
}
