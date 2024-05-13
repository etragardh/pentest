<?php

namespace Breakdance\DynamicData;

class PostContent extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Post Content';
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
        return 'post_content';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        ob_start();
        the_content();
        return StringData::fromString(ob_get_clean());
    }

    /**
     * @inheritDoc
     */
    function proOnly() {
        return false;
    }

}
