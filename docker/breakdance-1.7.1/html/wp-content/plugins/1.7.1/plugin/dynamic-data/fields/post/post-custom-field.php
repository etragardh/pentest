<?php

namespace Breakdance\DynamicData;

class PostCustomField extends StringField
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
        return 'Post';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'post_custom_field';
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
        return ['string', 'url', 'google_map'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        if (empty($attributes['key'])) {
            return StringData::emptyString();
        }
        $value = get_post_meta(get_the_ID(), $attributes['key'], true);
        if (!$value) {
            return StringData::emptyString();
        }
        return StringData::fromString($value);
    }

    /**
     * @inheritDoc
     */
    function proOnly() {
        return false;
    }

}
