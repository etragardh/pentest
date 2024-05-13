<?php

namespace Breakdance\DynamicData;

class ToolsetPostField extends PostField {

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
        return $this->field['name'];
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Toolset';
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
        return ToolsetField::isFieldAvailableForPostType($this->field, $postType);
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'toolset_field_' . $this->field['slug'];
    }

    /**
     * @inheritDoc
     */
    public function getPost()
    {
        $postId = ToolsetField::getValue($this->field);
        return get_post($postId);
    }

}
