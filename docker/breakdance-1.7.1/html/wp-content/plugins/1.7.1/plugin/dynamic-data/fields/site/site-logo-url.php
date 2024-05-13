<?php

namespace Breakdance\DynamicData;

class SiteLogoURL extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Site Logo (URL)';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Site Info';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'site_logo_url';
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['url'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $logo_id = get_theme_mod('custom_logo');
        $url = wp_get_attachment_image_url($logo_id, 'full') ?? '';

        return StringData::fromString($url);
    }
}
