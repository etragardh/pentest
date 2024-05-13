<?php

namespace Breakdance\DynamicData;


class AcfCheckboxField extends AcfField
{
    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control(
                'separator',
                'Separator',
                ['type' => 'text']
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $separator = $attributes['separator'] ?? ', ';
        $fieldValue = AcfField::getValue($this->field);
        /**
         * $fieldValue can be any of the following depending
         * on the field type and output settings
         *   - string
         *   - string[]
         *   - array{ value: string, label: string }
         *   - array{ value: string, label: string }[]
         */
        if (is_string($fieldValue)) {
            return StringData::fromString($fieldValue);
        }
        if (is_array($fieldValue) && array_is_list($fieldValue)) {
            $arrayOfValues = array_map(static function($value) {
                if (is_string($value)) {
                    return $value;
                }
                if (is_array($value) && array_key_exists('value', $value)) {
                    return $value['value'];
                }
                return '';
            }, $fieldValue);
            $valueAsString = implode($separator, $arrayOfValues);
            return StringData::fromString($valueAsString);
        }
        if (is_array($fieldValue) && array_key_exists('value', $fieldValue)) {
            return StringData::fromString($fieldValue['value']);
        }
        return StringData::emptyString();
    }
}
