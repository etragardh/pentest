<?php

namespace Breakdance\Forms\Actions;

use function Breakdance\Elements\control;

class Slack extends ApiAction
{

    /** @var string|null */
    protected $webhookUrl = null;

    /**
     * @param ?string $webhookUrl API Key
     */
    public function __construct($webhookUrl = null)
    {
        $this->webhookUrl = $webhookUrl;
    }

    /**
     * @return string
     */
    public static function name()
    {
        return 'Slack';
    }

    /**
     * @return string
     */
    public static function slug()
    {
        return 'slack';
    }

    /**
     * @return string[]
     */
    public function getHeaders()
    {
        return ['Content-Type' => 'application/json'];
    }

    /**
     * @return array
     */
    public function controls()
    {
        return [
            control(
                'api_key_input',
                'Use Slack Webhook',
                [
                    'type' => 'api_key_input',
                    'layout' => 'vertical',
                    'apiKeyOptions' => [
                        'apiKeyName' => BREAKDANCE_SLACK_WEBHOOK_URL_NAME,
                        'apiKeyLabel' => 'Webhook URL'
                    ]
                ]),

            control('pre_text', 'Message text', [
                'type' => 'text',
                'layout' => 'vertical',
                'placeholder' => 'Latest registration',
            ]),

            control('title', 'Title', [
                'type' => 'text',
                'layout' => 'vertical',
                'placeholder' => 'New user registration',
            ]),

            control('description', 'Description', [
                'type' => 'text',
                'layout' => 'vertical',
            ]),

            control('include_form_data', 'Include form data', [
                'type' => 'toggle',
            ]),

            control('include_timestamp', 'Include timestamp', [
                'type' => 'toggle',
            ]),

            control('color', 'Border color', [
                'type' => 'color',
            ]),
        ];
    }

    /**
     * @param FormData $form
     * @param FormSettings $settings
     * @param FormExtra $extra
     * @return ActionSuccess|ActionError|array<array-key, ActionSuccess|ActionError>
     */
    public function run($form, $settings, $extra)
    {
        $this->webhookUrl = self::getApiKeyFromApiKeyInput($settings['actions']['slack']['api_key_input'], BREAKDANCE_SLACK_WEBHOOK_URL_NAME);

        $isApiKeySetAndValid = self::isApiKeySet($this->webhookUrl);

        if ($isApiKeySetAndValid !== true) {
            return $isApiKeySetAndValid;
        }

        $settingsData = $settings['actions']['slack'];

        /** @var string $page_url */
        $page_url = $_POST['referrer'] ?? site_url();
        // remove the "FF" from the color that ColorInput adds
        $color = isset($settingsData['color']) ? substr($settingsData['color'], 0, -2) : '#9c0244';

        $newMessageData = [
            'attachments' => [
                [
                    'text' => $settingsData['description'] ?? 'New form submission received',
                    'pretext' => $settingsData['pre_text'] ?? '',
                    'title' => $settingsData['title'] ?? 'New form submission',
                    'color' => $color,
                    'title_link' => $page_url,
                    'ts' => $settingsData['include_timestamp'] ? time() : null,
                    'fields' => $settingsData['include_form_data']
                        ? array_map(
                            fn($field) => [
                                'title' => $field['label'],
                                'value' => $field['value'] ?? $field['advanced']['id'],
                                'short' => true,
                            ],
                            $form
                        ) : null
                ]
            ]
        ];

        /** @var string $webhookUrl */
        $webhookUrl = $this->webhookUrl;

        $requestData = wp_json_encode($newMessageData);

        if (!is_string($requestData)) {
            $this->addContext('Request Data', $requestData);
            return [
                'type' => 'error',
                'message' => 'Error encoding request data',
            ];
        }

        $handledResponse = $this->request($webhookUrl, 'POST', $requestData);

        if (array_key_exists('error', $handledResponse)) {
            /** @var string $error */
            $error = $handledResponse['error'];

            return [
                'type' => 'error',
                'message' => $error,
                'response' => $handledResponse
            ];
        }

        return [
            'type' => 'success',
            'response' => $handledResponse
        ];
    }

    /**
     * @param ?string $webhookUrl
     * @return ActionSuccess|ActionError
     */
    public static function validateApiKey($webhookUrl)
    {
        $isApiKeySetAndValid = self::isApiKeySet($webhookUrl);

        if ($isApiKeySetAndValid !== true) {
            return $isApiKeySetAndValid;
        }

        return ['type' => 'success', 'message' => 'ok'];
    }

    /**
     * @param ?string $webhookUrl
     * @return true|ActionError
     */
    public static function isApiKeySet($webhookUrl)
    {
        if (!$webhookUrl || empty($webhookUrl)) {
            return [
                'type' => 'error',
                'message' => 'Webhook URL is not set.'
            ];
        }


        if (
            strpos($webhookUrl, 'https://hooks.slack.com/services/') === false) {
            return [
                'type' => 'error',
                'message' => 'Invalid Slack Webhook URL'
            ];
        }

        return true;
    }
}
