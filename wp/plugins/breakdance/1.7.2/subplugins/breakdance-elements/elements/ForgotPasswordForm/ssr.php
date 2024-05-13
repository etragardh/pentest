<?php
/**
 * @var array $propertiesData
 */

$content = $propertiesData['content'];
$labels = $content['form']['labels'] ?? [];

$fields = [
    [
        'type'  => 'text',
        'label' => $labels['username_label'] ?? '',
        'advanced' => [
            'id' => 'user_login',
            'required' => true,
            'placeholder' => $labels['username_placeholder'] ?? '',
        ],
        'attributes' => [
            'autocomplete' => 'username',
            'autocapitalize' => 'off',
        ]
    ]
];

\Breakdance\Forms\Render\renderForm($fields, $propertiesData, 'forgot_password');
