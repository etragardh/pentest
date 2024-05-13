<?php

namespace Breakdance\Forms\Actions;

use function Breakdance\Elements\control;
use function Breakdance\Elements\repeaterControl;

class Popup extends Action
{

    /**
     * @inheritDoc
     */
    public static function name()
    {
        return 'Popup';
    }

    /**
     * @inheritDoc
     */
    public static function slug()
    {
        return 'popup';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            repeaterControl('popups_on_success', 'On Success',[
                control('popup', 'Popup', [
                    'type' => 'dropdown',
                    'layout' => 'vertical',
                    'placeholder' => 'No popup selected',
                    'dropdownOptions' => [
                        'populate' => [
                            'fetchDataAction' => 'breakdance_get_popups',
                        ],
                    ],
                ]),
                control('action', 'Popup Action', [
                    'type' => 'dropdown',
                    'layout' => 'vertical',
                    'placeholder' => 'No selected',
                    'items' => [
                        ['text' => "Open", 'value' => "open"],
                        ['text' => "Close", 'value' => "close"],
                        ['text' => "Toggle", 'value' => "toggle"]
                    ]
                ]),
            ], [
                'repeaterOptions' => [
                    'titleTemplate' => 'Popup',
                    'defaultTitle' => 'Popup',
                    'buttonName' => 'Add Popup'
                ]
            ]),
            repeaterControl('popups_on_error', 'On Error',[
                control('popup', 'Popup', [
                    'type' => 'dropdown',
                    'layout' => 'vertical',
                    'placeholder' => 'No popup selected',
                    'dropdownOptions' => [
                        'populate' => [
                            'fetchDataAction' => 'breakdance_get_popups',
                        ],
                    ],
                ]),
                control('action', 'Popup Action', [
                    'type' => 'dropdown',
                    'layout' => 'vertical',
                    'placeholder' => 'No selected',
                    'items' => [
                        ['text' => "Open", 'value' => "open"],
                        ['text' => "Close", 'value' => "close"],
                        ['text' => "Toggle", 'value' => "toggle"]
                    ]
                ]),
            ], [
                'repeaterOptions' => [
                    'titleTemplate' => 'Popup',
                    'defaultTitle' => 'Popup',
                    'buttonName' => 'Add Popup'
                ]
            ])
        ];
    }

    /**
     * @inheritDoc
     */
    public function run($form, $settings, $extra)
    {
        // Do nothing on purpose, log the JS that was executed
        $this->addContext('Popups', $settings['actions']['popup']);
        return ['type' => 'success'];
    }
}
