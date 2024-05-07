<?php

namespace Breakdance\DynamicData;

class AcfFileOembedField extends AcfOembedField {

    public function returnTypes()
    {
        return ['video'];
    }

    public function slug()
    {
        return 'acf_file_oembed_field_' . $this->field['key'];
    }

    public function handler($attributes): OembedData
    {
        $value = AcfField::getValue($this->field);
        if (is_string($value)) {
            return OembedData::fromOembedUrl($value);
        }
        if (is_array($value) && array_key_exists('url', $value)) {
            return OembedData::fromOembedUrl($value['url']);
        }

        return OembedData::emptyOembed();
    }
}
