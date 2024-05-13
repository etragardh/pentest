<?php

namespace Breakdance\DynamicData;

class AuthorEmail extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Author Email';
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
        return 'author_email';
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

        return StringData::fromString(get_the_author_meta('user_email', $author_id));
    }
}
