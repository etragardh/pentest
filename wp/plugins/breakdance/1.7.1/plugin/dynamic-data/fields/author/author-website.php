<?php

namespace Breakdance\DynamicData;

class AuthorWebsite extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Author Website';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Author';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'author_website';
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
        global $post;
        if (!$post) {
            return StringData::emptyString();
        }
        $author_id = $post->post_author;
        return StringData::fromString(get_the_author_meta('user_url', $author_id));
    }
}
