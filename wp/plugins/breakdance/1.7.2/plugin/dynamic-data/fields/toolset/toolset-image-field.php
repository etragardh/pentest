<?php

namespace Breakdance\DynamicData;

class ToolsetImageField extends ImageField {

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
        return 'toolset_image_' . $this->field['slug'];
    }

    public function handler($attributes): ImageData
    {
        $image_url = ToolsetField::getValue($this->field, ['url' => true]);
        $attachment_id = attachment_url_to_postid($image_url);
        return ImageData::fromAttachmentId($attachment_id);
    }
}
