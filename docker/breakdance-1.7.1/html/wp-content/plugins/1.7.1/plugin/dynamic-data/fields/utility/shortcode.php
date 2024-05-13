<?php

namespace Breakdance\DynamicData;

class Shortcode extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Shortcode';
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
        return 'shortcode';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('shortcode', 'Shortcode', [
                'type' => 'text',
                'layout' => 'vertical',
                'textOptions' => ['multiline' => true],
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        if (!array_key_exists('shortcode', $attributes)) {
            return StringData::emptyString();
        }

        ob_start();
        $output = do_shortcode($attributes['shortcode']);

        $shortcodeOutput = ob_get_clean();
        if ($shortcodeOutput) {
            return StringData::fromString($shortcodeOutput);
        }

        return StringData::fromString($output);
    }
}
