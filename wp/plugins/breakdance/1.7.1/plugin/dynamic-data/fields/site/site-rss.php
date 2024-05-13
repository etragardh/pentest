<?php

namespace Breakdance\DynamicData;

class SiteRss extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'RSS URL';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Site Info';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'site_rss';
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
                    ['text' => 'Atom URL', 'value' => 'atom_url'],
                    ['text' => 'RDF URL', 'value' => 'rdf_url'],
                    ['text' => 'RSS URL', 'value' => 'rss_url'],
                    ['text' => 'RSS2 URL', 'value' => 'rss2_url'],
                    ['text' => 'Pingback URL', 'value' => 'pingback_url'],
                    ['text' => 'Comments Atom URL', 'value' => 'comments_atom_url'],
                    ['text' => 'Comments RSS2 URL', 'value' => 'comments_rss2_url'],
                ]
            ])
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultAttributes()
    {
        return [
            'type' => 'rss2_url',
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::fromString(get_bloginfo($attributes['type']));
    }
}
