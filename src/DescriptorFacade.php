<?php

namespace BugbirdCo\Yoke;

use BugbirdCo\Yoke\Models\Descriptor\Action\Action;
use BugbirdCo\Yoke\Models\Descriptor\Descriptor;
use Illuminate\Support\Facades\Facade;

/**
 * Class DescriptorFacade
 * @package BugbirdCo\Yoke
 *
 * @method static void register()
 * @method Action act(string|Action $action, ...$arguments)
 *
 * @see Descriptor
 */
class DescriptorFacade extends Facade
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
