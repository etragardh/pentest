<?php

namespace Breakdance\DynamicData;

class MetaboxPostField extends PostField {

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
        return 'Metabox';
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
        return MetaboxField::isFieldAvailableForPostType($this->field, $postType);
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        $isSettingsPage = $this->field['is_settings_page'] ?? false;
        if ($isSettingsPage) {
            return 'metabox_post_field_' . $this->field['settings_page'] . '_' . $this->field['id'];
        }
        return 'metabox_post_field_' . $this->field['id'];
    }

    public function defaultAttributes()
    {
        return [
            'post_field' => 'post_title'
        ];
    }

    public function getPost()
    {
        $postId = MetaboxField::getValue($this->field);
        return get_post($postId);
    }

}
