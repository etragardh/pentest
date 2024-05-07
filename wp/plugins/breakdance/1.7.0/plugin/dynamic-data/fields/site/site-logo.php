<?php

namespace Breakdance\DynamicData;

class SiteLogo extends ImageField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Site Logo';
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
        return 'site_logo';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): ImageData
    {
        $logo_id = get_theme_mod( 'custom_logo' );
        return ImageData::fromAttachmentId($logo_id);
    }
}
