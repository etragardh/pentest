<?php

namespace Breakdance\DynamicData;

class MetaboxMapField extends MetaboxField {
    /**
     * @return string[]
     */
    public function returnTypes()
    {
        return ['string', 'google_map'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $map = MetaboxField::getValue($this->field);
        if (!$map) {
            return StringData::fromString('');
        }
        return StringData::fromString($map['latitude'] . ',' . $map['longitude']);
    }
}
