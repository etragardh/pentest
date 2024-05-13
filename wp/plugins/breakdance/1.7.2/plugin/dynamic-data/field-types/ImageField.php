<?php

namespace Breakdance\DynamicData;

abstract class ImageField extends Field {

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['image_url'];
    }

    public function fallbackControl()
    {
        return \Breakdance\Elements\control('fallback_image', 'Fallback Image', [
            'type' => 'wpmedia',
            'layout' => 'vertical',
        ]);
    }

    /**
     * @inheritDoc
     */
    abstract public function handler($attributes): ImageData;

}
