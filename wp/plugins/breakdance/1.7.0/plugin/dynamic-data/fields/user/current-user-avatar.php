<?php

namespace Breakdance\DynamicData;

class CurrentUserAvatar extends ImageField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'User Avatar';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Current User';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'user_avatar';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): ImageData
    {
        $user_id = get_current_user_id();

        $avatarSizes = [
            // The largest available size in Gravatar is 2048x2048
            'full' => get_avatar_data($user_id, ['size' => 2048])
        ];

        $availableSizes = \Breakdance\Media\Sizes\getAvailableSizes();
        foreach ($availableSizes as $availableSize) {
            if (array_key_exists('width', $availableSize)) {
                $avatarSizes[$availableSize['slug']] = get_avatar_data($user_id, ['size' => $availableSize['width']]);
            }
        }

        $imageData = new ImageData();
        $imageData->sizes = $avatarSizes;
        return $imageData;
    }
}
