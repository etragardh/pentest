<?php

namespace Breakdance\Forms\Actions;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\inlineRepeaterControl;
use function Breakdance\Elements\repeaterControl;

class ActiveCampaign extends ApiAction
{

    /** @var ?string */
    protected $baseUrl = null;

    /** @var ?string */
    protected $apiKey = null;

    /**
     * @param ?string $apiKey
     * @param ?string $apiUrl
     */
    public function __construct($apiKey = null, $apiUrl = null)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $apiUrl;
    }

    /**
     * @return string
     */
    public static function name()
    {
        return 'ActiveCampaign';
    }

    /**
     * @return string
     */
    public static function slug()
    {
        return 'activecampaign';
    }

    /**
     * @return void
     */
    public static function registerAjaxHandlers()
    {
        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_activecampaign_accounts',
            /**
             * @return array
             */
            function () {
                /** @var FormRequestContext $requestdata */
                $requestdata = $_POST['requestData'] ?? [];
                return self::getLists($requestdata);
            },
            'edit'
        );

        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_activecampaign_fields',
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
    }

    /**
     * @return array
     */
    public function controls()
    {
        return [
            control(
                'api_key_input',
                'Use ActiveCampaign API Key',
                [
                    'type' => 'api_key_input',
                    'layout' => 'vertical',
                    'apiKeyOptions' => [
                        'apiKeyName' => BREAKDANCE_ACTIVECAMPAIGN_API_KEY_NAME,
                        'urlKeyName' => BREAKDANCE_ACTIVECAMPAIGN_URL_NAME
                    ]
                ]),

            control('account', 'Account', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'placeholder' => 'No account selected',
                'dropdownOptions' => [
                    'populate' => [
                        'fetchDataAction' => 'breakdance_fetch_activecampaign_accounts',
                        'fetchContextPath' => 'content.actions.activecampaign',
                        'refetchPaths' => ['content.actions.activecampaign.api_key_input'],
                    ],
                ],
            ]),

            controlSection('field_mapping', 'Field Mapping', [
                repeaterControl('fields', 'Custom Fields', [
                    control('activecampaign_field', 'Field', [
                        'type' => 'dropdown',
                        'layout' => 'vertical',
                        'placeholder' => 'No field selected',
                        'dropdownOptions' => [
                            'populate' => [
                                'fetchDataAction' => 'breakdance_fetch_activecampaign_fields',
                                'fetchContextPath' => 'content.actions.activecampaign',
                                'refetchPaths' => ['content.actions.activecampaign.form', 'content.actions.activecampaign.api_key_input'],
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
                        ],
                    ]),
                ])
            ],
                ['condition' => ['path' => 'content.actions.activecampaign.account', 'operand' => 'is set', 'value' => '']]
            ),
        ];
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl ? $this->baseUrl . '/api/3' : '';
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Api-Token' => $this->apiKey,
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
        $this->apiKey = self::getApiKeyFromApiKeyInput($settings['actions']['activecampaign']['api_key_input'], BREAKDANCE_ACTIVECAMPAIGN_API_KEY_NAME);
        $this->baseUrl = self::getApiKeyFromApiKeyInput($settings['actions']['activecampaign']['api_key_input'], BREAKDANCE_ACTIVECAMPAIGN_URL_NAME);

        $isApiKeySetAndValid = self::isApiKeySet(['apiKey' => $this->apiKey, 'apiUrl' => $this->baseUrl]);

        if ($isApiKeySetAndValid !== true) {
            return $isApiKeySetAndValid;
        }

        $mergeFields = \Breakdance\Forms\getMappedFieldValuesFromFormData(
            $settings['actions']['activecampaign']['field_mapping']['fields'],
            $form,
            'activecampaign_field'
        );

        $contactInfo = [];

        // Add all default fields to the subscriber info and unset them from mergeFields
        foreach ($this->fields as $field){
            if (isset($mergeFields[$field['value']])){
                /** @var string */
                $contactInfo[$field['value']] = $mergeFields[$field['value']];

                unset($mergeFields[$field['value']]);
            }
        }

        $fieldValues = array_map(
            fn($id, $value) => ['field' => $id, 'value' => $value],
            array_keys($mergeFields),
            $mergeFields
        );

        $contactInfo = array_merge(
            $contactInfo,
            [
                'fieldValues' => $fieldValues,
            ]
        );


        if (!isset($contactInfo['email'])) {
            $this->addContext('Contact Information', $contactInfo);
            return [
                'type' => 'error',
                'message' => 'Email address is required.'
            ];
        }

        $response = $this->request("/contacts", 'POST', json_encode(['contact' => $contactInfo]));

        if (array_key_exists('error', $response)) {
            /** @var string $error */
            $error = $response['error'];

            return [
                'type' => 'error',
                'message' => $error,
                'response' => $response,
            ];
        }

        return [
            'type' => 'success',
            'response' => $response
        ];
    }

    /**
     * @param array{apiKey: string|null, apiUrl: string|null} $apiKeyAndUrl
     * @return ActionSuccess|ActionError
     */
    public static function validateApiKey($apiKeyAndUrl)
    {
        $isApiKeySetAndValid = self::isApiKeySet($apiKeyAndUrl);

        if ($isApiKeySetAndValid !== true) {
            return $isApiKeySetAndValid;
        }

        return self::getSuccessOrErrorFromApiKeyValidationResponse(
            self::fetchLists($apiKeyAndUrl['apiKey'] ?? null, $apiKeyAndUrl['apiUrl'] ?? null)
        );
    }

    /**
     * @psalm-suppress ImplementedParamTypeMismatch
     * @param array{apiKey: string|null, apiUrl: string|null}  $apiKeyAndUrl
     * @return true|ActionError
     */
    public static function isApiKeySet($apiKeyAndUrl)
    {
        if (!isset($apiKeyAndUrl['apiKey']) || !$apiKeyAndUrl['apiKey'] || empty($apiKeyAndUrl['apiKey'])) {
            return [
                'type' => 'error',
                'message' => 'API key is not set.'
            ];
        }

        if (!isset($apiKeyAndUrl['apiUrl']) || !$apiKeyAndUrl['apiUrl'] || empty($apiKeyAndUrl['apiUrl'])) {
            return [
                'type' => 'error',
                'message' => 'API URL is not set.'
            ];
        }

        $isApiUrlValid = filter_var($apiKeyAndUrl['apiUrl'], FILTER_VALIDATE_URL);

        if (!$isApiUrlValid){
            return [
                'type' => 'error',
                'message' => 'Invalid API URL. You can find the correct one in your ActiveCampaign settings.'
            ];
        }

        return true;
    }

    /**
     * @param ?string $apiKey
     * @param ?string $apiUrl
     * @return array{lists: array{name: string, stringid: string}[]} $response
     */
    public static function fetchLists($apiKey, $apiUrl){
        /** @var array{lists: array{name: string, stringid: string}[]} */
        $lists =  (new self($apiKey, $apiUrl))->request( '/lists');

        return $lists;
    }

    /**
     * @param FormRequestContext $requestData
     * @return DropdownData[]
     */
    public static function getLists($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_ACTIVECAMPAIGN_API_KEY_NAME);
        $apiUrl = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_ACTIVECAMPAIGN_URL_NAME);

        if (!$apiKey || !$apiUrl) {
            return [];
        }

        $response = self::fetchLists($apiKey, $apiUrl);

        if (array_key_exists('error', $response)) {
            return [];
        }

        return array_map(
            function ($account) {
                return [
                    'text' => $account['name'],
                    'value' => $account['stringid']
                ];
            },
            $response['lists'] ?? []
        );
    }

    /** @var array{text: string, value: string}[] */
    public $fields = [
        [
            'text' => 'Email',
            'value' => 'email'
        ],
        [
            'text' => 'First Name',
            'value' => 'firstName'
        ],
        [
            'text' => 'Last Name',
            'value' => 'lastName'
        ],
        [
            'text' => 'Phone',
            'value' => 'phone'
        ],
    ];


    /**
     * @param FormRequestContext $requestData
     * @return DropdownData[]
     */
    public static function getCustomFields($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_ACTIVECAMPAIGN_API_KEY_NAME);
        $apiUrl = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_ACTIVECAMPAIGN_URL_NAME);

        if (!$apiKey || !$apiUrl) {
            return [];
        }

        $self = new self($apiKey, $apiUrl);
        /**
         * @var array{fields: array{title: string, id: string}[]} $response
         */
        $response = $self->request('/fields');

        if (array_key_exists('error', $response)) {
            return [];
        }

        return array_merge(
            $self->fields,
            array_map(
                static function ($field) {
                    return [
                        'text' => $field['title'],
                        'value' => $field['id']
                    ];
                },
                $response['fields']
            )
        );
    }
}
