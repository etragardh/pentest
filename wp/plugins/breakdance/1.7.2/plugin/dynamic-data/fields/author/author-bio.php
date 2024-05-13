<?php

namespace Breakdance\DynamicData;

class AuthorBio extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Author Bio';
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
        return 'author_bio';
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
        return StringData::fromString(get_the_author_meta('description', $author_id));
    }
}
