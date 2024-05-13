<?php

namespace Breakdance\DynamicData;

class HomeUrl extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Home URL';
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
        return 'home_url';
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['url', 'string'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::fromString(get_bloginfo('url'));
    }
}

