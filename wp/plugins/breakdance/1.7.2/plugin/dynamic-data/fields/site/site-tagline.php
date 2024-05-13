<?php

namespace Breakdance\DynamicData;

class SiteTagline extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Site Tagline';
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
        return 'site_tagline';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::fromString(get_bloginfo('description'));
    }
}
