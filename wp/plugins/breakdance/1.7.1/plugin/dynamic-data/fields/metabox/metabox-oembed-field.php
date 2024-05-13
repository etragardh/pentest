<?php

namespace Breakdance\DynamicData;

class MetaboxOembedField extends OembedField
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
    public function slug()
    {
        $isSettingsPage = $this->field['is_settings_page'] ?? false;
        if ($isSettingsPage) {
            return 'metabox_oembed_field_' . $this->field['settings_page'] . '_' . $this->field['id'];
        }
        return 'metabox_oembed_field_' . $this->field['id'];
    }


    /**
     * @param string $postType
     * @return bool
     */
    public function availableForPostType($postType)
    {
        return MetaboxField::isFieldAvailableForPostType($this->field, $postType);
    }

    public function handler($attributes): OembedData
    {
        $oembedValue = MetaboxField::getValue($this->field);
        if (empty($oembedValue)) {
            return OembedData::emptyOembed();
        }

        if (is_string($oembedValue) && filter_var($oembedValue, FILTER_VALIDATE_URL)) {
            return OembedData::fromOembedUrl($oembedValue);
        }

        if (is_array($oembedValue)) {
            $video = reset($oembedValue);
            if (is_array($video) && array_key_exists('src', $video)) {
                $oembedData = new OembedData();
                $oembedData->embedUrl = $video['src'];
                $oembedData->mime = $video['type'];
                $oembedData->type = 'video';
                $oembedData->url = $video['src'];
                return $oembedData;
            }
        }

        return OembedData::emptyOembed();
    }
}
