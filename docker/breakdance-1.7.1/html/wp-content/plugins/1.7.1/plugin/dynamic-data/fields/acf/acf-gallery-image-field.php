<?php

namespace Breakdance\DynamicData;

class AcfGalleryImageField extends ImageField
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
        return AcfField::isFieldAvailableForPostType($this->field, $postType);
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'acf_gallery_image_field_' . $this->field['key'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): ImageData
    {
        $imageIds = AcfField::getValue($this->field, false);
        if (empty($imageIds)) {
            ImageData::emptyImage();
        }
        $imageIndex = $attributes['image_index'] ?? 0;
        if (isset($imageIds[$imageIndex])) {
            return ImageData::fromAttachmentId($imageIds[$imageIndex]);
        }

        return ImageData::emptyImage();
    }
}
