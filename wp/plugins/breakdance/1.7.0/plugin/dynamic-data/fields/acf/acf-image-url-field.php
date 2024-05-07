<?php

namespace Breakdance\DynamicData;

class AcfImageUrlField extends AcfField {

    public function handler($attributes): StringData
    {
        $attachment_id = AcfField::getValue($this->field, false);
        $attachment_url = wp_get_attachment_image_url($attachment_id, 'full') ?? '';

        return StringData::fromString($attachment_url);
    }
}
