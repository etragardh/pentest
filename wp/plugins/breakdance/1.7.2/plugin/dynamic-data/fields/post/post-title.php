<?php

namespace Breakdance\DynamicData;

class PostTitle extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Post Title';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Post';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'post_title';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::fromString(get_the_title());
    }

    /**
     * @inheritDoc
     */
    function proOnly() {
        return false;
    }

}
