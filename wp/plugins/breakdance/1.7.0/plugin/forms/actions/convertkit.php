<?php

namespace Breakdance\Forms\Actions;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\inlineRepeaterControl;
use function Breakdance\Elements\repeaterControl;

class ConvertKit extends ApiAction
{

    /** @var string */
    protected $baseUrl = "https://api.convertkit.com/v3";

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
        return 'ConvertKit';
    }

    /**
     * @return string
     */
    public static function slug()
    {
        return 'convertkit';
    }

    /**
     * @return void
     */
    public static function registerAjaxHandlers()
    {
        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_convertkit_forms',
            /**
             * @return array
             */
            function () {
                /** @var FormRequestContext $requestdata */
                $requestdata = $_POST['requestData'] ?? [];
                return self::getForms($requestdata);
            },
            'edit'
        );

        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_convertkit_fields',
            /**
             * @return array
             */
            function () {
                /** @var FormRequestContext $requestdata */
                $requestdata = $_POST['requestData'] ?? [];
                return self::getCustomFields($requestdata);
            },
            'edit'
        );

        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_convertkit_tags',
            /**
             * @return array
             */
            function () {
                /** @var FormRequestContext $requestdata */
                $requestdata = $_POST['requestData'] ?? [];
                return self::getTags($requestdata);
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
                'Use ConvertKit API Key',
                [
                    'type' => 'api_key_input',
                    'layout' => 'vertical',
                    'apiKeyOptions' => [
                        'apiKeyName' => BREAKDANCE_CONVERTKIT_API_KEY_NAME
                    ]
                ]),

            control('form', 'Form', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'placeholder' => 'No form selected',
                'dropdownOptions' => [
                    'populate' => [
                        'fetchDataAction' => 'breakdance_fetch_convertkit_forms',
                        'fetchContextPath' => 'content.actions.convertkit',
                        'refetchPaths' => ['content.actions.convertkit.api_key_input'],
                    ],
                ],
            ]),

            controlSection('field_mapping', 'Field Mapping', [
                repeaterControl('fields', 'Custom Fields', [
                    control('convertkit_field', 'Field', [
                        'type' => 'dropdown',
                        'layout' => 'vertical',
                        'placeholder' => 'No field selected',
                        'dropdownOptions' => [
                            'populate' => [
                                'fetchDataAction' => 'breakdance_fetch_convertkit_fields',
                                'fetchContextPath' => 'content.actions.convertkit',
                                'refetchPaths' => ['content.actions.convertkit.form', 'content.actions.convertkit.api_key_input'],
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
                ['condition' => ['path' => 'content.actions.convertkit.form', 'operand' => 'is set', 'value' => '']]
            ),

            inlineRepeaterControl('tags', 'Tags', [
                control('tag', 'Tag', [
                    'type' => 'dropdown',
                    'layout' => 'vertical',
                    'placeholder' => 'No tag selected',
                    'dropdownOptions' => [
                        'populate' => [
                            'fetchDataAction' => 'breakdance_fetch_convertkit_tags',
                            'fetchContextPath' => 'content.actions.convertkit',
                            'refetchPaths' => ['content.actions.convertkit.api_key_input'],
                        ],
                    ]
                ]),
            ],
                ['condition' => ['path' => 'content.actions.convertkit.form', 'operand' => 'is set', 'value' => '']]
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
            'Accept' => 'application/json',
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
        $this->apiKey = self::getApiKeyFromApiKeyInput($settings['actions']['convertkit']['api_key_input'], BREAKDANCE_CONVERTKIT_API_KEY_NAME);

        $isApiKeySetAndValid = self::isApiKeySet($this->apiKey);

        if ($isApiKeySetAndValid !== true) {
            return $isApiKeySetAndValid;
        }

        $mergeFields = \Breakdance\Forms\getMappedFieldValuesFromFormData(
            $settings['actions']['convertkit']['field_mapping']['fields'],
            $form,
            'convertkit_field'
        );

        $form_id = $settings['actions']['convertkit']['form'];
        $tagsArray = $settings['actions']['convertkit']['tags'];
        $tags = null;

        /** @var ?string */
        $email = $mergeFields['email'] ?? false;
        /** @var ?string */
        $first_name = $mergeFields['first_name'] ?? null;

        /**
         * @psalm-suppress RedundantConditionGivenDocblockType
         */
        if ($tagsArray && count($tagsArray)) {
            // convertKit expects an int[]
            $tags = array_reduce($tagsArray,
                /**
                 * @param array $acc
                 * @param array{tag: string} $tagArray
                 */
                function ($acc, $tagArray) {
                $acc[] = (int)$tagArray['tag'];

                return $acc;
            }, []);
        }

        if (!$email) {
            return [
                'type' => 'error',
                'message' => 'Email address is required.'
            ];
        }

        unset($mergeFields['email']);
        unset($mergeFields['first_name']);

        $response = $this->request("/forms/{$form_id}/subscribe", 'POST', json_encode([
            'api_key' => $this->apiKey,
            'email' => $email,
            'first_name' => $first_name,
            'fields' => $mergeFields,
            'tags' => $tags
        ]));

        if (array_key_exists('error', $response)) {
            /** @var string $error */
            $error = $response['error'];

            return [
                'type' => 'error',
                'message' => 'ConvertKit Error: ' . $error,
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

        // ConvertKit doesn't provide a "ping/validate key" endpoint, so we use a real GET endpoint as the next best thing
        $response = (new self($apiKey))->request('/forms', 'GET', [
                'api_key' => $apiKey,
            ]
        );

        return self::getSuccessOrErrorFromApiKeyValidationResponse($response);
    }

    /**
     * @param FormRequestContext $requestData
     * @return DropdownData[]
     */
    public static function getForms($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_CONVERTKIT_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }

        /** @var array{forms: array{name: string, id: string}[] | null } $response */
        $response = (new self($apiKey))->request('/forms', 'GET',
            [
                'api_key' => $apiKey,
            ]
        );

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
            $response['forms'] ?? []
        );
    }

    /**
     * @param FormRequestContext $requestData
     * @return DropdownData[]
     */
    public static function getCustomFields($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_CONVERTKIT_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }

        /** @var array{custom_fields: array{label: string, key: string}[] | null } $response */
        $response = (new self($apiKey))->request("/custom_fields", 'GET', [
                'api_key' => $apiKey,
            ]
        );

        if (!empty($response['custom_fields'])) {
            $fields = array_map(
                static function ($field) {
                    return [
                        'text' => $field['label'],
                        'value' => $field['key']
                    ];
                },
                $response['custom_fields'] ?? []
            );
        } else {
            $fields = [];
        }

        $fields[] = [
            'text' => 'Email',
            'value' => 'email'
        ];
        $fields[] = [
            'text' => 'First Name',
            'value' => 'first_name'
        ];

        return $fields;
    }

    /**
     * @param FormRequestContext $requestData
     * @return DropdownData[]
     */
    public static function getTags($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_CONVERTKIT_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }

        /** @var array{tags: array{name: string, id: string}[] | null } $response */
        $response = (new self($apiKey))->request("/tags", 'GET', [
            'api_key' => $apiKey,
        ]);

        if (!$response['tags']) {
            return [];
        }

        return array_map(
            static function ($field) {
                return [
                    'text' => $field['name'],
                    'value' => (string)$field['id']
                ];
            },
            $response['tags'] ?? []
        );
    }
}
