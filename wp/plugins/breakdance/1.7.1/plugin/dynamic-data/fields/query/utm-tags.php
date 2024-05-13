<?php
namespace Breakdance\DynamicData;

class UtmTags extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Common UTM Tags';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'URL & Query';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'common_utm_tags';
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['string', 'query'];
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('parameter_name', 'UTM Tag', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => [
                    ['text' => 'Source', 'value' => 'utm_source'],
                    ['text' => 'Medium', 'value' => 'utm_medium'],
                    ['text' => 'Campaign', 'value' => 'utm_campaign'],
                    ['text' => 'Term', 'value' => 'utm_term'],
                    ['text' => 'Content', 'value' => 'utm_content'],
                ]
            ]),
        ];
    }

    public function handler($attributes): StringData
    {
        if (!array_key_exists('parameter_name', $attributes)) {

            return StringData::fromString('');
        }
        $queryParameter = filter_input(INPUT_GET, $attributes['parameter_name'], FILTER_SANITIZE_SPECIAL_CHARS) ?: '';

        return StringData::fromString($queryParameter);
    }
}
