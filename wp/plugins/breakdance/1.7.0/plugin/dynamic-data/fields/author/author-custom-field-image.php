<?php

namespace Breakdance\DynamicData;

class AuthorCustomFieldImage extends ImageField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Custom Field (Image URL)';
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
        return 'author_custom_field_image';
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
        return [
            'image_url'
        ];
    }
    /**
     * @inheritDoc
     */
    public function handler($attributes): ImageData
    {
        global $post;

        if (empty($attributes['key']) || !$post) {
            return ImageData::emptyImage();
        }

        $author_id = $post->post_author;
        $urlOrAttachmentId = get_user_meta($author_id, $attributes['key'], true);
        if (filter_var($urlOrAttachmentId, FILTER_VALIDATE_URL)) {
            $url = strip_shortcodes($urlOrAttachmentId); // Security - strip_shortcodes: https://github.com/soflyy/breakdance/issues/4186
            return ImageData::fromUrl($url);
        }

        return ImageData::fromAttachmentId($urlOrAttachmentId);
    }
}
