<?php

namespace Breakdance\DynamicData;

class ToolsetUrlImageField extends ImageField {

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
        return 'toolset_image_' . $this->field['name'];
    }

    public function handler($attributes): ImageData
    {
        $imageUrl = ToolsetField::getValue($this->field, ['output' => 'raw']);
        return ImageData::fromUrl($imageUrl);
    }
}
