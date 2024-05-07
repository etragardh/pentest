<?php

namespace Breakdance\DynamicData;

class MetaboxImageFileField extends MetaboxImageField {

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

    public function handler($attributes): ImageData
    {
        $value = MetaboxField::getValue($this->field);

        // Metabox file fields can be a single
        // file URL, an array of file urls
        // or an array of attachment data

        if (is_string($value)) {
            return ImageData::fromUrl($value);
        }

        $fileIndex = $attributes['file_index'] ?? 0;
        if (is_array($value)) {
            $valueWithNumericKeys = array_values($value);
            if (!array_key_exists($fileIndex, $valueWithNumericKeys)) {
                return ImageData::emptyImage();
            }

            $valueAtIndex = $valueWithNumericKeys[$fileIndex];
            if (is_string($valueAtIndex)) {
                ImageData::fromUrl($valueAtIndex);
            }

            if (is_array($valueAtIndex) && array_key_exists('url', $valueAtIndex)) {
                return ImageData::fromArray($valueAtIndex);
            }
        }

        return ImageData::emptyImage();
    }
}
