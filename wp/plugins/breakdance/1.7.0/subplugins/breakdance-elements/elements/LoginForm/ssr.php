<?php
/**
 * @var array $propertiesData
 */

$content = $propertiesData['content'];
$labels = $content['form']['labels'];

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
        'type'  => 'password',
        'label' => $labels['password_label'],
        'advanced' => [
            'id' => 'user_password',
            'required' => true,
            'placeholder' => $labels['password_placeholder'] ?? '',
        ],
        'attributes' => [
            'autocomplete' => 'current-password',
            'autocapitalize' => 'off',
        ],
    ],
];

if ($content['form']['remember_me']) {
    $remember = [
        'type' => 'checkbox',
        'label' => null,
        'options' => [
            [
                'value' => true,
                'label' => $labels['remember_me']
            ],
        ],
        'advanced' => [
            'id' => 'remember',
            'required' => false,
        ]
    ];

    $fields[] = $remember;
}

if ($content['form']['lost_password']) {
    $lostPasswordUrl = wp_lostpassword_url();
    $index = array_key_last($fields);

    if ($content['form']['custom_lost_password_page'] ?? false) {
        $lostPasswordUrl = $content['form']['custom_lost_password_page'];
    }

    $fields[$index]['advanced']['append'] = '<a class="breakdance-form-link breakdance-form-link--password" href="' . $lostPasswordUrl . '">'. $labels['lost_password'] .'</a>';
}


\Breakdance\Forms\Render\renderForm($fields, $propertiesData, 'login');
