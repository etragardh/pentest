<?php

namespace Breakdance\DynamicData;

class ToolsetOembedField extends OembedField
{
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
     * @inheritDoc
     */
    public function slug()
    {
        return 'toolset_video_' . $this->field['slug'];
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
    public function handler($attributes): OembedData
    {
        if ($this->field['is_sub_field']) {
            $post = LoopController::getInstance()->get();
        } else {
            $post = get_post();
        }

        if (!$post) {
            return OembedData::emptyOembed();
        }

        if (!class_exists('Types_Field_Gateway_Wordpress_Post')) {
            return OembedData::emptyOembed();
        }

        // Get the field value from Types
        $gateway = new \Types_Field_Gateway_Wordpress_Post();
        $oembedField = $gateway->get_field_user_value($post->ID, $this->field['slug']);
        if (empty($oembedField)) {
            return OembedData::emptyOembed();
        }
        // get_field_user_value returns an array of URLs
        // so we need to get the first element
        [$oembedUrl] = $oembedField;
        if (empty($oembedUrl)) {
            return OembedData::emptyOembed();
        }

        return OembedData::fromOembedUrl($oembedUrl);
    }
}
