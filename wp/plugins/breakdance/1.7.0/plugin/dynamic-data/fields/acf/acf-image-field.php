<?php

namespace Breakdance\DynamicData;

class AcfImageField extends ImageField {

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
        return 'acf_image_' . $this->field['key'];
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
    public function handler($attributes): ImageData
    {
        $urlOrAttachmentId = AcfField::getValue($this->field, false);
        if (filter_var($urlOrAttachmentId, FILTER_VALIDATE_URL)) {
            return ImageData::fromUrl($urlOrAttachmentId);
        }

        return ImageData::fromAttachmentId($urlOrAttachmentId);
    }
}
