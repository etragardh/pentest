<?php

namespace Breakdance\DynamicData;

class ToolsetLinkField extends ToolsetField
{

    public function controls()
    {
        return [
            \Breakdance\Elements\control('toolset_link_target', 'Target', [
                    'type' => 'dropdown',
                    'items' => [
                        ['text' => 'Blank', 'value' => '_blank'],
                        ['text' => 'Self', 'value' => '_self'],
                        ['text' => 'Parent', 'value' => '_parent'],
                        ['text' => 'Top', 'value' => '_top'],
                    ],
                ]
            ),
        ];
    }

    public function defaultAttributes()
    {
        return ['toolset_link_target' => '_self'];
    }

    public function handler($attributes): StringData
    {
        $linkTarget = $attributes['toolset_link_target'] ?? '_self';
        $url = ToolsetField::getValue($this->field, ['target' => $linkTarget]);
        return StringData::fromString($url);
    }
}
