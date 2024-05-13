<?php

namespace Breakdance\Elements\UniversalControls;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;


/**
 * @return Control
 */
function getAttributesHtmlControl()
{
    return repeaterControl(
        'attributes',
        'Attributes',
        [
            control('name', 'Name',
                [
                    'type' => 'text',
                    'layout' => 'vertical',
                    'textOptions' => ['validationFunctionName' => 'validateHtmlAttributeName']
                ]
            ),
            control('value', 'Value',
                [
                    'type' => 'text',
                    'layout' => 'vertical',
                ]
            ),
        ],
        [
            'repeaterOptions' => [
                'titleTemplate' => '{name}',
                'defaultTitle' => 'Attribute',
                'buttonName' => 'Add attribute'
            ],
        ]
    );
}


/**
 * @param \Breakdance\Elements\Element $element
 * @return Control|null
 */
function getTagHtmlControl($element)
{

    if (!$element::tagControlPath() && count($element::tagOptions())) {
        $dropdownItemsOfTagOptions = array_map(
            /**
             * @param string $tag
             * @return array{text: string, value: string}
             */
            function ($tag) {
                return [
                    'text' => $tag,
                    'value' => $tag,
                ];
            },
            $element::tagOptions()
        );

        return control(
            'tag',
            'Tag',
            ['type' => 'dropdown', 'items' => $dropdownItemsOfTagOptions]
        );
    }

    return null;
}

/**
 * @return Control
 */
function getIdHtmlControl()
{
    return control(
        'id',
        'ID',
        [
            'type' => 'text',
            'layout' => 'vertical',
            'placeholder' => 'my_awesome_element',
            'textOptions' => [
                'validationFunctionName' => 'validateHtmlId'
            ]
        ]
    );
}
