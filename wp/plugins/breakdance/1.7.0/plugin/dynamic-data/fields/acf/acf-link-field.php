<?php

namespace Breakdance\DynamicData;

class AcfLinkField extends AcfField {

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['string', 'url'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $value = AcfField::getValue($this->field, false);
        if (is_string($value)) {
            return StringData::fromString($value);
        }
        if (is_array($value) && array_key_exists('url', $value)) {
            return StringData::fromString($value['url']);
        }

        return StringData::emptyString();
    }
}
