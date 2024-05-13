<?php

namespace Breakdance\DynamicData;

class AuthorAvatar extends ImageField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Author Avatar';
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
        return 'author_avatar';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): ImageData
    {
        $post = get_post();
        if (!$post) {
            return ImageData::emptyImage();
        }
        $author = $post->post_author;

        $avatarSizes = [
            // The largest available size in Gravatar is 2048x2048
            'full' => get_avatar_data($author, ['size' => 2048])
        ];

        $availableSizes = \Breakdance\Media\Sizes\getAvailableSizes();
        foreach ($availableSizes as $availableSize) {
            if (array_key_exists('width', $availableSize)) {

                $avatarSizes[$availableSize['slug']] = get_avatar_data($author, ['size' => $availableSize['width']]);
            }
        }
        $imageData = new ImageData();
        $imageData->sizes = $avatarSizes;
        return $imageData;
    }
}
