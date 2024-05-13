<?php

namespace Breakdance\DynamicData;

class CurrentUserCustomField extends StringField
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
        return 'Current User';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'user_custom_field';
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
            ])
        ];
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['string', 'url', 'google_map'];
    }

    public function handler($attributes): StringData
    {
        $user_id = get_current_user_id();

        if (empty($attributes['key']) || $user_id === 0) {
            return StringData::emptyString();
        }

        $customFieldValue = strip_shortcodes(get_user_meta($user_id, $attributes['key'], true)); // Security - strip_shortcodes: https://github.com/soflyy/breakdance/issues/4186
        return StringData::fromString($customFieldValue);
    }
}
