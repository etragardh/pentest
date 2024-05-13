<?php

namespace Breakdance\DynamicData;

class ArchiveDescription extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Archive Description';
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
        return 'archive_description';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::fromString(get_the_archive_description());
    }
}
