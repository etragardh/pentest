<?php

namespace Breakdance\DynamicData;

class MetaboxImageUrlField extends StringField {

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
            return 'metabox_image_url_' . $this->field['settings_page'] . '_' . $this->field['id'];
        }
        return 'metabox_image_url_' . $this->field['id'];
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
    public function handler($attributes): StringData
    {
        $imageFieldValue = MetaboxField::getValue($this->field);
        if (empty($imageFieldValue)) {
            return StringData::emptyString();
        }

        if (is_array($imageFieldValue) && array_key_exists('url', $imageFieldValue)) {
            return StringData::fromString($imageFieldValue['url']);
        }

        $indexedImages = array_values($imageFieldValue);
        $imageIndex = $attributes['image_index'] ?? 0;
        if (isset($indexedImages[$imageIndex])) {
            return StringData::fromString($indexedImages[$imageIndex]['url']);
        }

        return StringData::emptyString();
    }
}
