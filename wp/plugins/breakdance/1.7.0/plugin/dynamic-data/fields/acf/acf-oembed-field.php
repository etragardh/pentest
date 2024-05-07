<?php

namespace Breakdance\DynamicData;

class AcfOembedField extends OembedField
{

    public array $field;

    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * @inheritDoc
     */
    public function label()
    {
        return $this->field['label'];
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'ACF';
    }

    /**
     * @inheritDoc
     */
    public function subcategory()
    {
        return $this->field['group'];
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        if ($this->field['type'] === 'url') {
            return 'acf_oembed_' . $this->field['key'];
        }
        // preserve original slug  for other field types
        // to maintain backwards compatibility for existing fields
        return 'acf_field_' . $this->field['key'];
    }


    /**
     * @param string $postType
     * @return bool
     */
    public function availableForPostType($postType)
    {
        return AcfField::isFieldAvailableForPostType($this->field, $postType);
    }

    public function handler($attributes): OembedData
    {
        $oembedValue = AcfField::getValue($this->field, false);
        if (empty($oembedValue)) {
            return OembedData::emptyOembed();
        }
        return OembedData::fromOembedUrl($oembedValue);
    }
}
