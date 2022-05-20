<?php

namespace BugbirdCo\Yoke\Models\Descriptor\Size;

use BugbirdCo\Cabinet\Model;

abstract class Size extends Model
{
    public static function consumeModel($data)
    {
        if (is_array($data[0]))
            return AbsoluteSize::make(['width' => $data[0], 'height' => $data[1]]);

        return is_object($data) ? $data : RelativeSize::make(['size' => $data]);
    }
}