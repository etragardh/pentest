<?php

namespace Breakdance\DynamicData;

class SiteTitle extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Site Title';
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
        return 'site_title';
    }

    public function handler($attributes): StringData
    {
        return StringData::fromString(get_bloginfo('name'));
    }
}
