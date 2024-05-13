<?php

namespace Breakdance\DynamicData;

class AcfFileImageField extends AcfImageField {

    public function returnTypes()
    {
        return ['image_url'];
    }

    public function slug()
    {
        return 'acf_file_image_field_' . $this->field['key'];
    }

    public function handler($attributes): ImageData
    {
        $value = AcfField::getValue($this->field);
        if (is_string($value)) {
            return ImageData::fromUrl($value);
        }
        if (is_array($value) && array_key_exists('url', $value)) {
            return ImageData::fromUrl($value['url']);
        }

        return ImageData::emptyImage();
    }
}
