<?php
/**
 * @var array $propertiesData
 */

$content = $propertiesData['content'];
$labels = $content['form']['labels'] ?? [];

$fields = [
    [
        'type'  => 'text',
        'label' => $labels['username_label'],
        'advanced' => [
            'id' => 'user_login',
            'required' => true,
            'placeholder' => $labels['username_placeholder'] ?? '',
        ],
        'attributes' => [
            'autocomplete' => 'username',
            'autocapitalize' => 'off',
        ]
    ],
    [
        'type'  => 'email',
        'label' => $labels['email_label'],
        'advanced' => [
            'id' => 'user_email',
            'required' => true,
            'placeholder' => $labels['email_placeholder'] ?? '',
        ]
    ],
    [
        'type'  => 'password',
        'label' => $labels['password_label'],
        'advanced' => [
            'id' => 'user_pass',
            'placeholder' => $labels['password_placeholder'] ?? '',
            'required' => true,
        ],
        'attributes' => [
            'autocomplete' => 'current-password',
            'autocapitalize' => 'off',
        ]
    ]
];

$autoLoginUser = $content['form']['auto_login_user'] ?? false;
if ($autoLoginUser) {
    $fields[] = [
        'type'  => 'hidden',
        'advanced' => [
            'id' => 'auto_login_user',
            'value' => true,
        ]
    ];
}

\Breakdance\Forms\Render\renderForm($fields, $propertiesData, 'register');
