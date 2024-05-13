<?php

namespace Breakdance\DynamicData;

class PostPermalink extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Post Permalink';
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
        return 'post_permalink';
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['string', 'url'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::fromString(get_the_permalink());
    }

    /**
     * @inheritDoc
     */
    function proOnly() {
        return false;
    }

}
