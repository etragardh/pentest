<?php

namespace Breakdance\DynamicData;

class AcfPostField extends PostField {

    public $field;

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
     * @param string $postType
     * @return bool
     */
    public function availableForPostType($postType)
    {
        return AcfField::isFieldAvailableForPostType($this->field, $postType);
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'acf_field_' . $this->field['key'];
    }

    public function getPost()
    {
        $value = AcfField::getValue($this->field);
        if (!$value) {
            return false;
        }
        return get_post($value);
    }

}
