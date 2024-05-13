<?php

namespace Breakdance\DynamicData;

class ToolsetUrlField extends ToolsetField {
    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['url'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $url = ToolsetField::getValue($this->field, ['output' => 'raw']);
        return StringData::fromString($url);
    }
}
