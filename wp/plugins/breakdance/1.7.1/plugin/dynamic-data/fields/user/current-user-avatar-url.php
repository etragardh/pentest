<?php

namespace Breakdance\DynamicData;

class CurrentUserAvatarUrl extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'User Avatar (URL)';
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
        return 'user_avatar_url';
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
            $user_id = get_current_user_id();
            return StringData::fromString(strip_shortcodes(get_avatar_url($user_id, ['size' => 2048]))); // Security - strip_shortcodes: https://github.com/soflyy/breakdance/issues/4186
    }
}
