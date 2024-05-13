<?php

namespace Breakdance\DynamicData;

abstract class OembedField extends Field
{

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['video'];
    }

    public function fallbackControl()
    {
        return \Breakdance\Elements\control(
            'fallback_video',
            'Fallback/Default Video',
            [
                'type' => 'video',
                'layout' => 'vertical',
            ]);
    }

    /**
     * @inheritDoc
     */
    abstract public function handler($attributes): OembedData;
}
