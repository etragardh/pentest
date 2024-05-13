<?php

namespace Breakdance\DynamicData;

class AuthorCustomFieldOembed extends OembedField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Custom Field (Oembed URL)';
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
        return 'author_custom_field_oembed';
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
    public function handler($attributes): OembedData
    {
        global $post;

        if (empty($attributes['key']) || !$post) {
            return OembedData::emptyOembed();
        }

        $author_id = $post->post_author;
        $url = get_user_meta($author_id, $attributes['key'], true);
        return OembedData::fromOembedUrl($url);
    }
}
