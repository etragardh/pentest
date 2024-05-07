<?php

namespace Breakdance\DynamicData;

class CurrentUserWebsite extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'User Website';
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
        return 'user_website';
    }

    public function handler($attributes): StringData
    {
        $data = new StringData;
        $user = wp_get_current_user();
        $data->value = $user->user_url;

        $data->value = strip_shortcodes($data->value); // Security - strip_shortcodes: https://github.com/soflyy/breakdance/issues/4186

        return $data;
    }
}
