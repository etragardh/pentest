<?php
namespace EssentialElements;

use function Breakdance\Elements\control;
use function Breakdance\Elements\repeaterControl;

function getTypesControls()
{
    $allowedMimeTypes = get_allowed_mime_types();
    $controls = [
        [
            'types' => ['file'],
            'controls' => [
                control('max_file_size', 'Max. File Size (MB)', [
                    'type' => 'number',
                ]),
                control('max_number_of_files', 'Max. Number of Files', [
                    'type' => 'number',
                ]),
                control('allowed_file_types', 'Allowed File Types', [
                    'type' => 'multiselect',
                    'layout' => 'vertical',
                    'searchable' => true,
                    'items' => array_map(static function ($mimeType, $fileType) {
                        return [
                            // display as comma separated list of extensions
                            'text' => str_replace('|', ', ', $fileType),
                            'value' => $mimeType,
                        ];
                    }, array_values($allowedMimeTypes), array_keys($allowedMimeTypes)),
                ]),
                control('drag_and_drop', 'Drag and Drop', [
                    'type' => 'toggle',
                ]),
            ],
        ],
        [
            'types' => ['select'],
            'controls' => [
                control('multiple', 'Multiple', [
                    'type' => 'toggle',
                    'layout' => 'inline',
                ]),
                control('rows_select', 'Rows', [
                    'type' => 'number',
                    'condition' => [
                        'path' => '%%CURRENTPATH%%.multiple',
                        'operand' => 'equals',
                        'value' => true,
                    ],
                ]),
            ],
        ],
        [
            'types' => ['select', 'radio', 'checkbox'],
            'controls' => [
                repeaterControl('options', 'Options',
                    [
                        control('label', 'Option Label', [
                            'type' => 'text',
                            'layout' => 'vertical',
                        ]),
                        control('value', 'Option Value', [
                            'type' => 'text',
                            'layout' => 'vertical',
                        ]),
                        control('selected', 'Selected', [
                            'type' => 'toggle',
                            'layout' => 'vertical',
                        ]),
                    ],
                    [
                        'repeaterOptions' => [
                            'titleTemplate' => '{label}',
                            'defaultTitle' => 'Option',
                            'buttonName' => 'Add option',
                        ],
                    ]
                ),
            ],
        ],
        [
            'types' => ['date'],
            'controls' => [
                control('min_date', 'Min Date', [
                    'type' => 'text',
                    'placeholder' => 'yyyy-mm-dd',
                ]),
                control('max_date', 'Max Date', [
                    'type' => 'text',
                    'placeholder' => 'yyyy-mm-dd',
                ]),
            ],
        ],
        [
            'types' => ['number'],
            'controls' => [
                control('min', 'Min', [
                    'type' => 'number',
                ]),
                control('max', 'Max', [
                    'type' => 'number',
                ]),
                control('step', 'Step', [
                    'type' => 'number',
                ]),
            ],
        ],
        [
            'types' => ['textarea'],
            'controls' => [
                control('rows_textarea', 'Rows', [
                    'type' => 'number',
                ]),
            ],
        ],
        [
            'types' => ['html'],
            'controls' => [
                control('html', 'HTML', [
                    'type' => 'code',
                    'layout' => 'vertical',
                    'codeOptions' => ['language' => 'html'],
                ]),
            ],
        ],
        [
            'types' => ['step'],
            'controls' => [
                control('previous_button_text', 'Previous Button Text', [
                    'type' => 'text',
                    'layout' => 'vertical',
                ]),
                control('next_button_text', 'Next Button Text', [
                    'type' => 'text',
                    'layout' => 'vertical',
                ]),
                control('step_icon', 'Icon', [
                    'type' => 'icon',
                    'layout' => 'vertical',
                ]),
            ],
        ],
    ];

    $output = [];

    foreach ($controls as $control) {
        foreach ($control['controls'] as $c) {
            if (empty($c['options']['condition'])) {
                $c['options']['condition'] = [
                    'path' => '%%CURRENTPATH%%.type',
                    'operand' => 'is one of',
                    'value' => $control['types'],
                ];
            }

            $output[] = $c;
        }
    }

    return $output;
}
