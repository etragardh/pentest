<?php

namespace Breakdance\Forms\Actions;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\inlineRepeaterControl;
use function Breakdance\Elements\repeaterControl;

class Drip extends ApiAction
{

    /** @var string */
    protected $baseUrl = "https://api.getdrip.com/v2";

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
        return 'Drip';
    }

    /**
     * @return string
     */
    public static function slug()
    {
        return 'drip';
    }

    /**
     * @return void
     */
    public static function registerAjaxHandlers()
    {
        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_drip_accounts',
            /**
             * @return array
             */
            function () {
                /** @var FormRequestContext $requestdata */
                $requestdata = $_POST['requestData'] ?? [];
                return self::getAccounts($requestdata);
            },
            'edit'
        );

        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_drip_fields',
            /**
             * @return array
             */
            function () {
                /** @var FormRequestContextDrip $requestdata */
                $requestdata = $_POST['requestData'] ?? [];
                return self::getCustomFields($requestdata);
            },
            'edit'
        );

        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_drip_tags',
            /**
             * @return array
             */
            function () {
                /** @var FormRequestContextDrip $requestdata */
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
                'Use Drip API Key',
                [
                    'type' => 'api_key_input',
                    'layout' => 'vertical',
                    'apiKeyOptions' => [
                        'apiKeyName' => BREAKDANCE_DRIP_API_KEY_NAME
                    ]
                ]),

            control('account', 'Account', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'placeholder' => 'No account selected',
                'dropdownOptions' => [
                    'populate' => [
                        'fetchDataAction' => 'breakdance_fetch_drip_accounts',
                        'fetchContextPath' => 'content.actions.drip',
                        'refetchPaths' => ['content.actions.drip.api_key_input'],
                    ],
                ],
            ]),

            controlSection('field_mapping', 'Field Mapping', [
                repeaterControl('fields', 'Custom Fields', [
                    control('drip_field', 'Field', [
                        'type' => 'dropdown',
                        'layout' => 'vertical',
                        'placeholder' => 'No field selected',
                        'dropdownOptions' => [
                            'populate' => [
                                'fetchDataAction' => 'breakdance_fetch_drip_fields',
                                'fetchContextPath' => 'content.actions.drip',
                                'refetchPaths' => ['content.actions.drip.form', 'content.actions.drip.api_key_input'],
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
                ['condition' => ['path' => 'content.actions.drip.account', 'operand' => 'is set', 'value' => '']]
            ),

            control('tags', 'Tag', [
                'type' => 'multiselect',
                'layout' => 'vertical',
                'placeholder' => 'No tag selected',
                'multiselectOptions' => [
                    'populate' => [
                        'fetchDataAction' => 'breakdance_fetch_drip_tags',
                        'fetchContextPath' => 'content.actions.drip',
                        'refetchPaths' => ['content.actions.drip.api_key_input'],
                    ],
                    'makeCombobox' => true
                ],
                'condition' => ['path' => 'content.actions.drip.account', 'operand' => 'is set', 'value' => '']
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
            'Authorization' => 'Basic ' . base64_encode($this->apiKey ?? ''),
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
        $this->apiKey = self::getApiKeyFromApiKeyInput($settings['actions']['drip']['api_key_input'], BREAKDANCE_DRIP_API_KEY_NAME);

        $isApiKeySetAndValid = self::isApiKeySet($this->apiKey);

        if ($isApiKeySetAndValid !== true) {
            return $isApiKeySetAndValid;
        }

        $mergeFields = \Breakdance\Forms\getMappedFieldValuesFromFormData(
            $settings['actions']['drip']['field_mapping']['fields'],
            $form,
            'drip_field'
        );

        $accountId = $settings['actions']['drip']['account'];

        $subscriberInfo = [];

        // Add all default fields to the subscriber info and unset them from mergeFields
        foreach ($this->fields as $field){
            if (isset($mergeFields[$field['value']])){
                /** @var string */
                $subscriberInfo[$field['value']] = $mergeFields[$field['value']];

                unset($mergeFields[$field['value']]);
            }
        }

        $subscriberInfo = array_merge(
            $subscriberInfo,
            [
            'custom_fields' => $mergeFields,
            'tags' => $settings['actions']['drip']['tags'] ?? [],
            ]
        );


        if (!isset($subscriberInfo['email'])) {
            return [
                'type' => 'error',
                'message' => 'Email address is required.'
            ];
        }

        $newContactData = [
            'subscribers' => [$subscriberInfo]
        ];

        $response = $this->request("/{$accountId}/subscribers", 'POST', json_encode($newContactData));


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

        return self::getSuccessOrErrorFromApiKeyValidationResponse(self::fetchAccounts($apiKey));
    }

    /**
     * @param ?string $apiKey
     * @return array{links: array, accounts: array{id: string, name: string}[]}|array{errors: array{code: string, message: string}[]} $response
     */
    public static function fetchAccounts($apiKey){
        /** @var array{links: array, accounts: array{id: string, name: string}[]}|array{errors: array{code: string, message: string}[]} $accounts */
        $accounts =  (new self($apiKey))->request('/accounts');

        return $accounts;
    }

    /**
     * @param FormRequestContext $requestData
     * @return DropdownData[]
     */
    public static function getAccounts($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_DRIP_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }

        $response = self::fetchAccounts($apiKey);

        if (array_key_exists('error', $response)) {
            return [];
        }

        return array_map(
            function ($account) {
                return [
                    'text' => $account['name'],
                    'value' => $account['id']
                ];
            },
            $response['accounts'] ?? []
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
            'value' => 'first_name'
        ],
        [
            'text' => 'Last Name',
            'value' => 'last_name'
        ],
        [
            'text' => 'Address 1',
            'value' => 'address1'
        ],
        [
            'text' => 'Address 2',
            'value' => 'address2'
        ],
        [
            'text' => 'City',
            'value' => 'city'
        ],
        [
            'text' => 'State',
            'value' => 'state'
        ],
        [
            'text' => 'Zip or Postcode',
            'value' => 'zip'
        ],
        [
            'text' => 'Country',
            'value' => 'country'
        ],
        [
            'text' => 'Phone',
            'value' => 'phone'
        ],
    ];


    /**
     * @param FormRequestContextDrip $requestData
     * @return DropdownData[]
     */
    public static function getCustomFields($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_DRIP_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }

        /** @var string $accountId */
        $accountId = $requestData['context']['account'];

        $self = new self($apiKey);
        /**
         * @var array{custom_field_identifiers: string[]} $response
         */
        $response = $self->request("/{$accountId}/custom_field_identifiers");

        if (array_key_exists('error', $response)) {
            return [];
        }

        return array_merge(
            $self->fields,
            array_map(
                static function ($field) {
                    return [
                        'text' => $field,
                        'value' => $field
                    ];
                },
                $response['custom_field_identifiers']
            )
        );
    }

    /**
     * @param FormRequestContextDrip $requestData
     * @return DropdownData[]
     */
    public static function getTags($requestData)
    {
        $apiKeyInput = $requestData['context']['api_key_input'] ?? null;

        $apiKey = self::getApiKeyFromApiKeyInput($apiKeyInput, BREAKDANCE_DRIP_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }

        $accountId = $requestData['context']['account'] ?? null;

        if (!$accountId) {
            return [];
        }

        /**
         * @var array{tags: string[]} $response
         */
        $response = (new self($apiKey))->request("/{$accountId}/tags");

        if (array_key_exists('error', $response)) {
            return [];
        }

        return array_map(
            static function ($tag) {
                return [
                    'text' => $tag,
                    'value' => $tag
                ];
            },
            $response['tags']
        );
    }
}
