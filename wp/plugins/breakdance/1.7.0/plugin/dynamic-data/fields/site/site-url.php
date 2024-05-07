<?php

namespace Breakdance\DynamicData;

class SiteUrl extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Site URL';
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
        return 'site_url';
    }

    public function returnTypes()
    {
        return ['url', 'string'];
    }

    public function handler($attributes): StringData
    {
        return StringData::fromString(get_bloginfo('wpurl'));
    }
}
