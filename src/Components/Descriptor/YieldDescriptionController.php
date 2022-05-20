<?php

namespace BugbirdCo\Yoke\Components\Descriptor;

class YieldDescriptionController
{
    public function __invoke()
    {
        return response()->json(app('yoke.descriptor'));
    }
}