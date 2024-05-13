<?php

namespace Breakdance\DynamicData;

class CurrentUserEmail extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'User Email';
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
        return 'user_email';
    }

    public function handler($attributes): StringData
    {
        $data = new StringData();
        $user = wp_get_current_user();
        $data->value = $user->user_email;

        $data->value = strip_shortcodes($data->value); // Security - strip_shortcodes: https://github.com/soflyy/breakdance/issues/4186

        return $data;
    }
}
