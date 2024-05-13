<?php

namespace Breakdance\Forms\Recaptcha;

use function Breakdance\APIKeys\getKey;
use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

/**
 * @param string $token
 * @param string $ip
 * @param string $action
 * @param string? $secret
 * @return bool
 */
function verify($token, $ip, $action = 'breakdance_submit', $secret = null)
{
    // reCAPTCHA v3 returns a score.
    // 1.0 is very likely a good interaction, 0.0 is very likely a bot
    // https://developers.google.com/recaptcha/docs/verify
    $threshold = 0.5;

    if (!$secret) {
        $secret = getKey(BREAKDANCE_RECAPTCHA_SECRET_KEY_NAME);
    }

    $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
        'body' => [
            'secret'   => $secret,
            'response' => $token,
            'remoteip' => $ip
        ]
    ]);

    if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
        return false;
    }

    /** @var ReCaptchaResponse $body */
    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    return $body['success'] && $body['score'] >= $threshold && $body['action'] == $action;
}

/**
 * @return array|Control
 */
function controls()
{

    $recaptchaControls = [
        control('enabled', 'Enable reCAPTCHA', [
            'type' => 'toggle'
        ]),
        control(
        'api_key_input',
        'Use reCAPTCHA API Key',
        [
            'type' => 'api_key_input',
            'layout' => 'vertical',
            'apiKeyOptions' => [
                'apiKeyLabel' => 'Secret Key',
                'apiKeyName' => BREAKDANCE_RECAPTCHA_SECRET_KEY_NAME,
                'urlKeyLabel' => 'Site Key',
                'urlKeyName' => BREAKDANCE_RECAPTCHA_SITE_KEY_NAME
            ],
            'condition' => [
                'path' => '%%CURRENTPATH%%.enabled',
                'operand' => 'equals',
                'value' => true,
            ],
        ]),
    ];

    return controlSection('recaptcha', 'reCAPTCHA', $recaptchaControls, null, 'popout');
}

/**
 * @param array{apiKey: string|null, apiUrl: string|null} $apiKeyAndUrl
 * @return ActionSuccess|ActionError
 */
function validateRecaptchaKeys($apiKeyAndUrl)
{
    $isApiKeySetAndValid = isApiKeySet($apiKeyAndUrl);
    if ($isApiKeySetAndValid !== true) {
        return $isApiKeySetAndValid;
    }

    /** @var \WP_Error|ReCaptchaResponse $response */
    $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
        'body' => [
            'secret'   => $apiKeyAndUrl['apiKey']
        ]
    ]);

    $isValidApiResponse = isValidApiResponse($response);
    if ($isValidApiResponse !== true) {
        return $isValidApiResponse;
    }

    return [
        'type'    => 'success',
        'message' => 'Secret Key is valid.'
    ];
}

/**
 * @param array{apiKey: string|null, apiUrl: string|null}  $apiKeyAndUrl
 * @return true|ActionError
 */
function isApiKeySet($apiKeyAndUrl) {
    if (!isset($apiKeyAndUrl['apiKey']) || !$apiKeyAndUrl['apiKey'] || empty($apiKeyAndUrl['apiKey'])) {
        return [
            'type' => 'error',
            'message' => 'Secret Key is not set.'
        ];
    }

    if (!isset($apiKeyAndUrl['apiUrl']) || !$apiKeyAndUrl['apiUrl'] || empty($apiKeyAndUrl['apiUrl'])) {
        return [
            'type' => 'error',
            'message' => 'Site Key is not set.'
        ];
    }

    return true;
}

/**
 * @param \WP_Error|ReCaptchaResponse $response
 * @return true|ActionError
 */
function isValidApiResponse($response) {
    if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
        return [
            'type' => 'error',
            'message' => 'Error response from API'
        ];
    }

    /** @var ReCaptchaResponse $responseData */
    $responseData = json_decode(wp_remote_retrieve_body($response), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'type'    => 'error',
            'message' => 'Error retrieving response from reCAPTCHA, please try again'
        ];
    }

    if (in_array('invalid-input-secret', $responseData['error-codes'])) {
        return [
            'type'    => 'error',
            'message' => 'Invalid Secret Key'
        ];
    }

    return true;
}
