<?php

namespace Breakdance\DynamicData;

abstract class GalleryField extends Field {

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['gallery'];
    }

    /**
     * @inheritDoc
     */
    abstract public function handler($attributes): GalleryData;

}
