<?php

namespace Breakdance\DynamicData;

class MetaboxVideoFileField extends MetaboxOembedField {

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

    public function handler($attributes): OembedData
    {
        $value = MetaboxField::getValue($this->field);

        // Metabox file fields can be a single
        // file URL, an array of file urls
        // or an array of attachment data

        if (is_string($value)) {
            return OembedData::fromOembedUrl($value);
        }

        $fileIndex = $attributes['file_index'] ?? 0;
        if (is_array($value)) {
            $valueWithNumericKeys = array_values($value);
            if (!array_key_exists($fileIndex, $valueWithNumericKeys)) {
                return OembedData::emptyOembed();
            }

            $valueAtIndex = $valueWithNumericKeys[$fileIndex];
            if (is_string($valueAtIndex)) {
                OembedData::fromOembedUrl($valueAtIndex);
            }

            if (is_array($valueAtIndex) && array_key_exists('url', $valueAtIndex)) {
                return OembedData::fromArray($valueAtIndex);
            }
        }

        return OembedData::emptyOembed();
    }
}
