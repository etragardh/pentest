<?php

namespace Breakdance\DynamicData;

class CurrentUserName extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'User Name';
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
        return 'user_name';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('name_field', 'Field', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge([
                    ['text' => 'Display Name', 'value' => 'display_name'],
                    ['text' => 'First Name', 'value' => 'first_name'],
                    ['text' => 'Last Name', 'value' => 'last_name'],
                ])
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultAttributes()
    {
        return [
            'name_field' => 'display_name',
        ];
    }

    public function handler($attributes): StringData
    {
        $data = new StringData;
        $data->value = '';

        $user = wp_get_current_user();
        if (!array_key_exists('name_field', $attributes) || $attributes['name_field'] === 'display_name') {
            $data->value = $user->display_name;
        }
        if ($attributes['name_field'] === 'first_name') {
            $data->value = $user->first_name;
        }
        if ($attributes['name_field'] === 'last_name') {
            $data->value = $user->last_name;
        }

        $data->value = strip_shortcodes($data->value); // Security - strip_shortcodes: https://github.com/soflyy/breakdance/issues/4186

        return $data;
    }
}
