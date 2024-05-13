<?php

namespace Breakdance\Forms\Actions;

use function Breakdance\Elements\control;

class Discord extends ApiAction
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
        return 'Discord';
    }

    /**
     * @return string
     */
    public static function slug()
    {
        return 'discord';
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
                'Use Discord Webhook',
                [
                    'type' => 'api_key_input',
                    'layout' => 'vertical',
                    'apiKeyOptions' => [
                        'apiKeyName' => BREAKDANCE_DISCORD_WEBHOOK_URL_NAME,
                        'apiKeyLabel' => 'Webhook URL'
                    ]
                ]),

            control('username', 'Bot Name', [
                'type' => 'text',
                'layout' => 'vertical',
                'placeholder' => 'Registration alerts',
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

            control('avatar', 'Message Icon', [
                'type' => 'wpmedia', 'layout' => 'vertical',
            ]),

            control('image', 'Main Image', [
                'type' => 'wpmedia', 'layout' => 'vertical',
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
        $this->webhookUrl = self::getApiKeyFromApiKeyInput($settings['actions']['discord']['api_key_input'], BREAKDANCE_DISCORD_WEBHOOK_URL_NAME);

        $isApiKeySetAndValid = self::isApiKeySet($this->webhookUrl);

        if ($isApiKeySetAndValid !== true) {
            return $isApiKeySetAndValid;
        }

        $settingsData = $settings['actions']['discord'];

        $avatar = $settingsData['avatar']['url'] ?? '';
        $image = $settingsData['image']['url'] ?? '';
        // remove the "FF" from the color that ColorInput adds, otherwise we can't turn it into decimal
        $color = isset($settingsData['color']) ? substr($settingsData['color'], 0, -2) : '#9c0244';
        // Discord only accepts colors as decimals (man wtf who designs this APIs)
        $decimalColor = hexdec( ltrim( $color, '#' ) );
        /** @var string $page_url */
        $page_url = $_POST['referrer'] ?? site_url();

        // https://discord.com/developers/docs/resources/channel#embed-object
        $embeds = [
            'title' => $settingsData['title'] ?? "New submission",
            'description' => $settingsData['description'] ?? "",
            'author' => [
                'name'     => $settingsData['username'] ?? "Breakdance Forms",
                'url'      => $page_url,
                'icon_url' => $avatar,
            ],
            'image' => [
                'url' => $image,
            ],
            'url' => $page_url,
            'color' => $decimalColor,
        ];

        if ( $settingsData['include_timestamp'] ?? false ) {
            $embeds['timestamp'] = gmdate( \DateTime::ISO8601 );
        }

        if ($settingsData['include_form_data'] ?? false){
            $embeds['fields'] = array_map(
                fn($field) => [
                    'name'   => $field['label'],
                    'value'  => $field['value'] ?? $field['advanced']['id'],
                    'inline' => false,
                ],
                $form
            );
        }

        /** @var string $webhookUrl */
        $webhookUrl = $this->webhookUrl;

        $requestData = wp_json_encode( ['embeds' => [ $embeds ]] );
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
            // old format, still supported
            strpos( $webhookUrl, 'https://discordapp.com/api/webhooks/') === false &&
            // current format
            strpos( $webhookUrl, 'https://discord.com/api/webhooks/') === false ) {
            return [
                'type' => 'error',
                'message' => 'Invalid Discord webhook URL'
            ];
        }

        return true;
    }
}
