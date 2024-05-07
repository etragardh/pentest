<?php

namespace Breakdance\Forms\Actions;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\inlineRepeaterControl;
use function Breakdance\Elements\repeaterControl;

class GetResponse extends ApiAction
{

    /** @var string  */
    protected $baseUrl = "https://api.getresponse.com/v3";

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
        return 'GetResponse';
    }

    /**
     * @return string
     */
    public static function slug()
    {
        return 'getresponse';
    }

    /**
     * @return void
     */
    public static function registerAjaxHandlers()
    {
        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_getresponse_list',
            /**
             * @return array
             */
            function () {
                /** @var FormRequestContext $requestdata */
                $requestdata = $_POST['requestData'] ?? [];
                return self::getList($requestdata);            },
            'edit'
        );

        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_getresponse_fields',
            /**
             * @return array
             */
            function () {
                /** @var FormRequestContext $requestdata */
                $requestdata = $_POST['requestData'] ?? [];
                return self::getCustomFields($requestdata);            },
            'edit'
        );

        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_getresponse_tags',
            /**
             * @return array
             */
            function () {
                /** @var FormRequestContext $requestdata */
                $requestdata = $_POST['requestData'] ?? [];
                return self::getTags($requestdata);            },
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
                'Use GetResponse API Key',
                [
                    'type' => 'api_key_input',
                    'layout' => 'vertical',
                    'apiKeyOptions' => [
                        'apiKeyName' => BREAKDANCE_GETRESPONSE_API_KEY_NAME
                    ]
                ]),

            control('list', 'List', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'placeholder' => 'No form selected',
                'dropdownOptions' => [
                    'populate' => [
                        'fetchDataAction' => 'breakdance_fetch_getresponse_list',
                        'fetchContextPath' => 'content.actions.getresponse',
                        'refetchPaths' => ['content.actions.getresponse.api_key_input'],
                    ],
                ],
            ]),

            controlSection('field_mapping', 'Field Mapping', [
                repeaterControl('fields', 'Custom Fields', [
                    control('getresponse_field', 'Field', [
                        'type' => 'dropdown',
                        'layout' => 'vertical',
                        'placeholder' => 'No field selected',
                        'dropdownOptions' => [
                            'populate' => [
                                'fetchDataAction' => 'breakdance_fetch_getresponse_fields',
                                'fetchContextPath' => 'content.actions.getresponse',
                                'refetchPaths' => ['content.actions.getresponse.form', 'content.actions.getresponse.api_key_input'],
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
                ['condition' => ['path' => 'content.actions.getresponse.list', 'operand' => 'is set', 'value' => '']]
            ),

            inlineRepeaterControl('tags', 'Tags', [
                control('tagId', 'Tag', [
                    'type' => 'dropdown',
                    'layout' => 'vertical',
                    'placeholder' => 'No tag selected',
                    'dropdownOptions' => [
                        'populate' => [
                            'fetchDataAction' => 'breakdance_fetch_getresponse_tags',
                            'fetchContextPath' => 'content.actions.getresponse',
                            'refetchPaths' => ['content.actions.getresponse.api_key_input'],
                        ],
                    ]
                ]),
            ],
                ['condition' => ['path' => 'content.actions.getresponse.list', 'operand' => 'is set', 'value' => '']]
            ),

            control('dayOfCycle', 'Day Of Cycle', [
                'type' => 'number',
                'layout' => 'inline',
                'condition' => ['path' => 'content.actions.getresponse.list', 'operand' => 'is set', 'value' => '']
            ])

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
            'X-Auth-Token' => "api-key {$this->apiKey}"
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
        $this->apiKey = self::getApiKeyFromApiKeyInput($settings['actions']['getresponse']['api_key_input'], BREAKDANCE_GETRESPONSE_API_KEY_NAME);

        $isApiKeySetAndValid = self::isApiKeySet($this->apiKey);

        if ($isApiKeySetAndValid !== true) {
            return $isApiKeySetAndValid;
        }

        $mergeFields = \Breakdance\Forms\getMappedFieldValuesFromFormData(
            $settings['actions']['getresponse']['field_mapping']['fields'],
            $form,
            'getresponse_field'
        );

        /** @var ?string */
        $email = $mergeFields['email'] ?? null;
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

        $customFields = array_map(
            function ($key, $value){
                return ['customFieldId' => $key, 'value' => [$value]];
            },
            array_keys($mergeFields),
            array_values($mergeFields)
        );

        // https://apireference.getresponse.com/#operation/createContact
        $newContactData = [
            'email' => $email,
            'name' => $name,
            'campaign' => [
                'campaignId' =>  $settings['actions']['getresponse']['list']
            ],
            'customFieldValues' => $customFields,
            'tags' => $settings['actions']['getresponse']['tags'] ?? [],
        ];

        if ($settings['actions']['getresponse']['dayOfCycle'] !== null) {
            $newContactData['dayOfCycle'] = $settings['actions']['getresponse']['dayOfCycle'];
        }

        $response = $this->request("/contacts", 'POST', json_encode($newContactData));

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

        return parent::getSuccessOrErrorFromApiKeyValidationResponse(self::fetchLists($apiKey));
    }

    /**
     * @param ?string $apiKey
     * @return array{campaignId: string, name: string}[]|
     */
    public static function fetchLists($apiKey){
        // GetResponse used to call List "campaigns"
        /** @var array{campaignId: string, name: string}[] $lists */
        $lists =  (new self($apiKey))->request('/campaigns');

        return $lists;
    }

    /**
     * @param FormRequestContext $requestData
     * @return DropdownData[]
     */
    public static function getList($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_GETRESPONSE_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }

        $response = self::fetchLists($apiKey);

        if (array_key_exists('error', $response)) {
            return [];
        }

        return array_map(
            function ($list) {
                return [
                    'text' => $list['name'],
                    'value' => (string) $list['campaignId']
                ];
            },
            $response
        );
    }

    /**
     * @param FormRequestContext $requestData
     * @return DropdownData[]
     */
    public static function getCustomFields($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_GETRESPONSE_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }

        /**
         * @var array{customFieldId: string, name: string}[] $response
         */
        $response = (new self($apiKey))->request("/custom-fields", 'GET');

        if (array_key_exists('error', $response)) {
            return [];
        }

        $fields = [
            [
                'text' => 'Email',
                'value' => 'email'
            ],
            [
                'text' => 'Name',
                'value' => 'name'
            ]
        ];



        $fields = array_merge(
            $fields,
            array_map(
                static function ($field) {
                    return [
                        'text' => $field['name'],
                        'value' => $field['customFieldId']
                    ];
                },
                $response
            )
        );

        return $fields;
    }

    /**
     * @param FormRequestContext $requestData
     * @return DropdownData[]
     */
    public static function getTags($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_GETRESPONSE_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }

        /**
         * @var array{tagId: string, name: string}[] $response
         */
        $response = (new self($apiKey))->request("/tags");

        if (array_key_exists('error', $response)) {
            return [];
        }

        return array_map(
            static function ($field) {
                return [
                    'text' => $field['name'],
                    'value' => $field['tagId']
                ];
            },
            $response
        );
    }
}
