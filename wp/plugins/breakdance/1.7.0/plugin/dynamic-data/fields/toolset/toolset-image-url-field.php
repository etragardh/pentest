<?php

namespace Breakdance\DynamicData;

class ToolsetImageUrlField extends ToolsetField {

    public function handler($attributes): StringData
    {
        $attachment_url = ToolsetField::getValue($this->field, ['url' => true]);
        return StringData::fromString($attachment_url);
    }
}
