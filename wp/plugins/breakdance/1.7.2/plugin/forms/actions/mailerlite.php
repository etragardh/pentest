<?php

namespace Breakdance\Forms\Actions;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;

class MailerLite extends ApiAction
{
    /** @var string */
    protected $baseUrl = "https://api.mailerlite.com/api/v2";

    /** @var ?string */
    protected $apiKey = null;

    /**
     * @param ?string $apiKey API Key
     */
    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public static function name()
    {
        return 'MailerLite';
    }

    /**
     * @return string
     */
    public static function slug()
    {
        return 'mailerlite';
    }

    /**
     * @return void
     */
    public static function registerAjaxHandlers()
    {
        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_mailerlite_groups',
            /**
             * @return array
             */
            function () {
                /** @var FormRequestContext $requestdata */
                $requestdata = $_POST['requestData'] ?? [];
                return self::getGroups($requestdata);
            },
            'edit'
        );

        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_mailerlite_fields',
            /**
             * @return array
             */
            function () {
                /** @var FormRequestContext $requestdata */
                $requestdata = $_POST['requestData'] ?? [];
                return self::getFields($requestdata);
            },
            'edit'
        );
    }

    /**
     * @return array
     */
    public function controls()
    {
        return [
            control(
                'api_key_input',
                'Use MailerLite API Key',
                [
                    'type' => 'api_key_input',
                    'layout' => 'vertical',
                    'apiKeyOptions' => [
                        'apiKeyName' => BREAKDANCE_MAILERLITE_API_KEY_NAME
                    ]
                ]),

            control('group', 'group', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'placeholder' => 'No group selected',
                'dropdownOptions' => [
                    'populate' => [
                        'fetchDataAction' => 'breakdance_fetch_mailerlite_groups',
                        'fetchContextPath' => 'content.actions.mailerlite',
                        'refetchPaths' => ['content.actions.mailerlite.api_key_input'],
                    ],
                ],
            ]),

            controlSection('field_mapping', 'Field Mapping', [
                repeaterControl('fields', 'Custom Fields', [
                    control('mailerlite_field', 'Field', [
                        'type' => 'dropdown',
                        'layout' => 'vertical',
                        'placeholder' => 'No field selected',
                        'dropdownOptions' => [
                            'populate' => [
                                'fetchDataAction' => 'breakdance_fetch_mailerlite_fields',
                                'fetchContextPath' => 'content.actions.mailerlite',
                                'refetchPaths' => ['content.actions.mailerlite.group', 'content.actions.mailerlite.api_key_input'],
                            ],
                        ]
                    ]),

                    control('formField', 'Form Field', [
                        'type' => 'dropdown',
                        'layout' => 'vertical',
                        'placeholder' => '',
                        'dropdownOptions' => [
                            'populate' => [
                                'path' => 'content.form.fields',
                                'text' => 'label',
                                'value' => 'advanced.id',
                            ]
                        ]
                    ]),
                ])
            ],
                ['condition' => ['path' => 'content.actions.mailerlite.group', 'operand' => 'is set', 'value' => '']]
            ),

            control('resubscribe', 'Reactivate subscriber', [
                    'type' => 'toggle',
                    'layout' => 'inline',
                    'condition' => ['path' => 'content.actions.mailerlite.group', 'operand' => 'is set', 'value' => '']
                ]
            )

        ];
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [
            'X-MailerLite-ApiKey' => $this->apiKey,
            'Content-Type' => 'application/json',
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
        $this->apiKey = self::getApiKeyFromApiKeyInput($settings['actions']['mailerlite']['api_key_input'], BREAKDANCE_MAILERLITE_API_KEY_NAME);

        $isApiKeySetAndValid = self::isApiKeySet($this->apiKey);

        if ($isApiKeySetAndValid !== true) {
            return $isApiKeySetAndValid;
        }

        $mergeFields = \Breakdance\Forms\getMappedFieldValuesFromFormData(
            $settings['actions']['mailerlite']['field_mapping']['fields'],
            $form,
            'mailerlite_field'
        );

        $form_id = $settings['actions']['mailerlite']['group'];

        /** @var ?string */
        $email = $mergeFields['email'] ?? false;
        /** @var ?string */
        $name = $mergeFields['name'] ?? null;

        if (!$email) {
            return [
                'type' => 'error',
                'message' => 'Email address is required.'
            ];
        }

        unset($mergeFields['email']);
        unset($mergeFields['name']);

        $response = $this->request("/groups/{$form_id}/subscribers", 'POST', json_encode([
            'email' => $email,
            'name' => $name,
            'fields' => $mergeFields,
            'resubscribe' => $settings['actions']['mailerlite']['resubscribe'] ?? false
        ]));

        if (array_key_exists('error', $response)) {
            /** @var string $error */
            $error = $response['error'];

            return [
                'type' => 'error',
                'message' => $error,
                'response' => $response
            ];
        }

        return [
            'type' => 'success',
            'response' => $response
        ];
    }

    /**
     * @param ?string $apiKey
     * @return ActionSuccess|ActionError
     */
    public static function validateApiKey($apiKey)
    {
        $isApiKeySetAndValid = self::isApiKeySet($apiKey);

        if ($isApiKeySetAndValid !== true) {
            return $isApiKeySetAndValid;
        }

        // MailerLite doesn't provide a "ping/validate key" endpoint, so we use a real GET endpoint as the next best thing
        $response = (new self($apiKey))->request('/groups');

        return self::getSuccessOrErrorFromApiKeyValidationResponse($response);
    }

    /**
     * @param FormRequestContext $requestData
     * @return DropdownData[]
     */
    public static function getGroups($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_MAILERLITE_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }

        /** @var array{name: string, id: int}[] $response */
        $response = (new self($apiKey))->request('/groups');

        if (array_key_exists('error', $response)) {
            return [];
        }

        return array_map(
            function ($list) {
                return [
                    'text' => $list['name'],
                    'value' => (string)$list['id']
                ];
            },
            $response
        );
    }

    /**
     * @param FormRequestContext $requestData
     * @return DropdownData[]
     */
    public static function getFields($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_MAILERLITE_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }

        /** @var array{key: string, title: string}[] $response */
        $response = (new self($apiKey))->request("/fields");

        if (array_key_exists('error', $response)) {
            return [];
        }


        return array_map(
            static function ($field) {
                return [
                    'text' => $field['title'],
                    'value' => $field['key']
                ];
            },
            $response
        );
    }
}
