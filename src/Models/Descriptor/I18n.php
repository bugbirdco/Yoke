<?php

namespace BugbirdCo\Yoke\Models\Descriptor;

use BugbirdCo\Cabinet\Model;

/**
 * @property string $value
 * @property string|null $i18n
 */
class I18n extends Model
{
    public static function consumeModel($data)
    {
        if (is_string($data))
            return static::make(['value' => $data]);

        return is_object($data) ? $data : static::make($data);
    }

    public function jsonSerialize()
    {
        return array_filter(static::attributes()->raw());
    }
}