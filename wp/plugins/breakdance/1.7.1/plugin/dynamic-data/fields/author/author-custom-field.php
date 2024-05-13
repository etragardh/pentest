<?php

namespace Breakdance\DynamicData;

class AuthorCustomField extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Custom Field';
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
        return 'author_custom_field';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('key', 'Key', [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['string', 'url', 'google_map'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        global $post;
        if (empty($attributes['key']) || !$post) {
            return StringData::emptyString();
        }
        $author_id = $post->post_author;
        return StringData::fromString(get_user_meta($author_id, $attributes['key'], true));
    }
}
