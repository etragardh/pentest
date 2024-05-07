<?php

namespace Breakdance\DynamicData;

class AdminEmail extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Admin Email';
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
        return 'admin_email';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::fromString(get_bloginfo('admin_email'));
    }
}
