<?php

namespace Breakdance\DynamicData;

class ArchiveTitle extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Archive Title';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Archive';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'archive_title';
    }


    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('disable_prefix', 'Disable Prefix', [
                'type' => 'toggle'
            ]),
        ];
    }


    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $disablePrefix = filter_var($attributes['disable_prefix'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if ($disablePrefix) {
            add_filter('get_the_archive_title_prefix', '__return_false');
        }

        $title = StringData::fromString(get_the_archive_title());

        if ($disablePrefix) {
            remove_filter('get_the_archive_title_prefix', '__return_false');
        }

        return $title;
    }
}
