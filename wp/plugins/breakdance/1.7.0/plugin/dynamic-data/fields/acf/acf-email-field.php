<?php

namespace Breakdance\DynamicData;

class AcfEmailField extends AcfField {
    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'acf_email_' . $this->field['key'];
    }

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
        $value = (string) AcfField::getValue($this->field);
        if (!empty($value)) {
            $value = 'mailto:' . $value;
        }
        return StringData::fromString($value);
    }
}
