<?php

namespace Breakdance\DynamicData;

class PostExcerpt extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Post Excerpt';
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
        return 'post_excerpt';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        ob_start();
        the_excerpt();
        return StringData::fromString(ob_get_clean());
    }

    /**
     * @inheritDoc
     */
    function proOnly() {
        return false;
    }

}
