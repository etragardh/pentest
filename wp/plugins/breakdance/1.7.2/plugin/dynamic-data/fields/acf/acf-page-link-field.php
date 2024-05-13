<?php

namespace Breakdance\DynamicData;

class AcfPageLinkField extends AcfField {

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
        $value = AcfField::getValue($this->field);
        if (is_string($value)) {
            return StringData::fromString($value);
        }
        if (is_array($value) && !empty($value)) {
            [$url] = $value;
            return StringData::fromString($url);
        }

        return StringData::emptyString();
    }
}
