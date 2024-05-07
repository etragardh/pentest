<?php

namespace Breakdance\DynamicData;

class AuthorAvatarUrl extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Author Avatar (URL)';
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
        return 'author_avatar_url';
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['url'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $post = get_post();
        if (!$post) {
            return StringData::emptyString();
        }
        $author = $post->post_author;
        $avatarUrl = get_avatar_url($author, ['size' => 2048]);
        if (!$avatarUrl) {
            return StringData::emptyString();
        }
        return StringData::fromString($avatarUrl);
    }
}

