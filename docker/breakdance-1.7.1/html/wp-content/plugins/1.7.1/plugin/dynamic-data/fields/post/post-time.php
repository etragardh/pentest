<?php

namespace Breakdance\DynamicData;

class PostTime extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Post Time';
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
        return 'post_time';
    }

    /**
     * @inheritDoc
     */
    public function defaultAttributes()
    {
        return [
            'type' => 'modified',
        ];
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('type', 'Type', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => [
                    ['text' => 'Post Published', 'value' => 'published'],
                    ['text' => 'Post Modified', 'value' => 'modified'],
                ]
            ]),

            \Breakdance\Elements\control('format', 'Format', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge(
                    [['text' => 'Default', 'value' => '']],
                    \Breakdance\DynamicData\get_time_formats(),
                    [
                        ['text' => 'Custom', 'value' => 'Custom'],
                        ['text' => 'Human', 'value' => 'Human']
                    ]
                )
            ]),
            \Breakdance\Elements\control('custom_format', 'Custom Format', [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.format',
                    'operand' => 'equals',
                    'value' => 'Custom'
                ]
            ])
        ];
    }

    public function handler($attributes): StringData
    {
        $value = $this->getDateFromAttributes($attributes);
        if (!is_string($value)) {
            return StringData::emptyString();
        }
        return StringData::fromString($value);
    }

    /**
     * @param $attributes
     * @return mixed|string|void
     */
    private function getDateFromAttributes($attributes)
    {
        $format = $attributes['format'] ?? '';
        if ($format === 'Custom') {
            $format = $attributes['custom_format'] ?? '';
        }
        $postTimeType = $attributes['type'] ?? 'modified';
        if ($postTimeType === 'published') {
            if ($format === 'Human') {
                return human_time_diff(get_the_time('U'));
            }
            return apply_filters('the_time', get_the_time($format));
        }
        if ($format === 'Human') {
            return human_time_diff(get_the_modified_time('U'));
        }
        return apply_filters('the_modified_time', get_the_modified_time($format), $format);
    }

    /**
     * @inheritDoc
     */
    function proOnly() {
        return false;
    }

}
