<?php

namespace BugbirdCo\Yoke\Models\Descriptor\Module;

use BugbirdCo\Yoke\Models\Descriptor\I18n;
use BugbirdCo\Yoke\Models\Descriptor\Size\AbsoluteSize;
use BugbirdCo\Yoke\Models\Descriptor\Size\Size;

/**
 * @property I18n|null $header
 * @property boolean $chrome
 * @property Size $size
 */
abstract class DialogModule extends PageModule
{
    public $exclude = ['type', 'size', 'header'];

    public static function type(): string
    {
        return 'dialogs';
    }

    public abstract static function chrome(): bool;

    public abstract static function size(): Size|array|string;

    public abstract static function header(): I18n|array|string|null;

    public static function make($attrs = []): Module
    {
        return parent::make($attrs + [
                'header' => static::header(),
                'chrome' => static::chrome(),
                'size' => static::size(),
            ]);
    }

    public function jsonSerialize()
    {
        return parent::jsonSerialize() + [
                'options' => [
                        'chrome' => $this->chrome,
                    ] + ($this->size instanceof AbsoluteSize
                        ? ['height' => $this->size->height, 'width' => $this->size->width]
                        : ['size' => strtoupper($this->size->size)]
                    ) + ($this->chrome ? ['header' => $this->header] : [])
            ];
    }
}