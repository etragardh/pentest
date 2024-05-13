<?php

namespace Breakdance\DynamicData;

class MetaboxUserField extends MetaboxField {

    /**
     * @inheritDoc
     */
    public function defaultAttributes()
    {
        return [
            'user_field' => 'display_name'
        ];
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('user_field', 'Field', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge([
                    ['text' => 'Display Name', 'value' => 'display_name'],
                    ['text' => 'Bio', 'value' => 'bio'],
                    ['text' => 'Custom Field', 'value' => 'custom_field'],
                ])
            ]),
            \Breakdance\Elements\control('custom_field_key', 'Custom Field Key', [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.user_field',
                    'operand' => 'equals',
                    'value' => 'custom_field'
                ]
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $userId = MetaboxField::getValue($this->field);
        $user = get_userdata($userId);
        if (!$user) {
            return StringData::emptyString();
        }
        $userField = $attributes['user_field'] ?? 'display_name';
        if ($userField === 'display_name') {
            return StringData::fromString(
                $user->display_name
            );
        }
        if ($userField === 'bio') {
            return StringData::fromString(
                $user->description
            );
        }
        if ($userField === 'custom_field' && array_key_exists('custom_field_key', $attributes)) {
            return StringData::fromString(
                get_user_meta($userId, $attributes['custom_field_key'], true)
            );
        }
        return StringData::emptyString();
    }
}
