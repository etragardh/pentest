<?php

namespace Breakdance\DynamicData;

class MetaboxFileField extends MetaboxField {

    public function returnTypes()
    {
        return ['string', 'url'];
    }

    public function controls() {
        return [
            \Breakdance\Elements\control(
                'file_index',
                'File Index',
                [
                    'type' => 'number',
                    'layout' => 'vertical',
                ]
            )
        ];
    }

    public function handler($attributes): StringData
    {
        $value = MetaboxField::getValue($this->field);

        // Metabox file fields can be a single
        // file URL, an array of file urls
        // or an array of attachment data

        if (is_string($value)) {
            return StringData::fromString($value);
        }

        $fileIndex = $attributes['file_index'] ?? 0;
        if (is_array($value)) {
            $valueNormalised = array_values($value);
            if (!array_key_exists($fileIndex, $valueNormalised)) {
                return StringData::emptyString();
            }

            $v = $valueNormalised[$fileIndex];
            if (is_string($v)) {
                StringData::fromString($v);
            }

            if (is_array($v) && array_key_exists('url', $v)) {
                return StringData::fromString($v['url']);
            }

        }

        return StringData::emptyString();
    }
}
