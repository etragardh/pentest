<?php

namespace Breakdance\DynamicData;

class RepeaterData extends FieldData
{

    public array $value = [];

    /**
     * @param mixed $attributes
     * @return array
     */
    public function getValue($attributes = []): array
    {
        return $this->value;
    }

    public function hasValue()
    {
        return !empty($this->value);
    }

    public static function fromArray(array $repeater): self
    {
        $repeaterData = new self;
        $repeaterData->value = $repeater;

        return $repeaterData;
    }
}
