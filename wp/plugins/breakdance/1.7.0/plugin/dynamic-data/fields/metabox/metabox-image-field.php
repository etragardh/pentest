<?php

namespace Breakdance\DynamicData;

use EssentialElements\Image;

class MetaboxImageField extends ImageField {

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
     * @inheritDoc
     */
    public function slug()
    {
        $isSettingsPage = $this->field['is_settings_page'] ?? false;
        if ($isSettingsPage) {
            return 'metabox_image_' . $this->field['settings_page'] . '_' . $this->field['id'];
        }
        return 'metabox_image_' . $this->field['id'];
    }

    /**
     * @inheritdoc
     */
    public function controls() {
        return [
            \Breakdance\Elements\control(
                'image_index',
                'Image Index',
                [
                    'type' => 'number',
                    'layout' => 'vertical',
                ]
            )
        ];
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
    public function handler($attributes): ImageData
    {
        $imageFieldValue = MetaboxField::getValue($this->field);
        if (empty($imageFieldValue)) {
            return ImageData::emptyImage();
        }

        if (!is_array($imageFieldValue) && filter_var($imageFieldValue, FILTER_VALIDATE_URL)) {
            return ImageData::fromUrl($imageFieldValue);
        }

        if (is_array($imageFieldValue) && array_key_exists('ID', $imageFieldValue)) {
            return ImageData::fromAttachmentId($imageFieldValue['ID']);
        }

        $imageIds = array_keys($imageFieldValue);
        $imageIndex = $attributes['image_index'] ?? 0;
        if (isset($imageIds[$imageIndex])) {
            return ImageData::fromAttachmentId($imageIds[$imageIndex]);
        }

        return ImageData::emptyImage();
    }
}
