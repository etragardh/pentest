<?php

namespace Breakdance\DynamicData;

class GalleryData extends FieldData
{

    public array $images = [];

    /**
     * @param mixed $attributes
     * @return array
     */
    public function getValue($attributes = []): array
    {
        return array_map(static function (ImageData $image) {
            return [
                'caption' => $image->caption,
                'image' => $image->getValue(),
                'type' => 'image',
            ];
        }, $this->images);
    }

    public function hasValue()
    {
        return !empty($this->images);
    }
}
