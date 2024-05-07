<?php

namespace Breakdance\DynamicData;

class Date extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Current Date';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Utility';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'date';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('format', 'Format', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge(
                    [['text' => 'Default', 'value' => '']],
                    \Breakdance\DynamicData\get_date_formats(),
                    [['text' => 'Custom', 'value' => 'Custom'], ['text' => 'Human', 'value' => 'Human']]
                ),
                [['text' => 'Custom', 'value' => 'Custom'], ['text' => 'Human', 'value' => 'Human']]
            ]),
            \Breakdance\Elements\control('custom_format', 'Custom Format', [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.format',
                    'operand' => 'equals',
                    'value' => 'Custom'
                ]
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultAttributes()
    {
        return [
            'format' => 'F j, Y'
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $format = $attributes['format'] ?? get_option('date_format');
        if (empty($format) || $format === 'Default') {
            $format = get_option('date_format');
        }
        if ($format === 'Custom') {
            $format = $attributes['custom_format'] ?? '';
        }
        if ($format === 'Human') {
            return StringData::fromString(human_time_diff(wp_date('U')));
        }
        return StringData::fromString(wp_date($format));
    }
}
