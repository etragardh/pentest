<?php

namespace Breakdance\Forms\Actions;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;

class MailChimp extends ApiAction {

    /** @var string API Base URL */
    protected $baseUrl = "https://%s.api.mailchimp.com/3.0/";

    /** @var ?string API Key */
    protected $apiKey = null;

    /**
     * @param ?string $apiKey API Key
     */
    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Get the displayable label of the action.
     * @return string
     */
    public static function name()
    {
        return 'MailChimp';
    }

    /**
     * Get the URL friendly slug of the action.
     * @return string
     */
    public static function slug()
    {
        return 'mailchimp';
    }

    /**
     * @return void
     */
    public static function registerAjaxHandlers()
    {
        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_mailchimp_lists',
            ['Breakdance\Forms\Actions\MailChimp', 'getLists'],
            'edit',
            false,
            [
                'args' => [
                    'requestData' => [
                        'filter' => FILTER_DEFAULT,
                        'flags' => FILTER_REQUIRE_ARRAY
                    ]
                ],
                'optional_args' => ['requestData'],
            ]
        );

        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_mailchimp_fields',
            ['Breakdance\Forms\Actions\MailChimp', 'getFields'],
            'edit',
            false,
            [
                'args' => [
                    'requestData' => [
                        'filter' => FILTER_DEFAULT,
                        'flags' => FILTER_REQUIRE_ARRAY
                    ]
                ]
            ]
        );

        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_mailchimp_interest_categories',
            ['Breakdance\Forms\Actions\MailChimp', 'getInterestCategories'],
            'edit',
            false,
            [
                'args' => [
                    'requestData' => [
                        'filter' => FILTER_DEFAULT,
                        'flags' => FILTER_REQUIRE_ARRAY
                    ]
                ]
            ]
        );

        \Breakdance\AJAX\register_handler(
            'breakdance_fetch_mailchimp_interests',
            ['Breakdance\Forms\Actions\MailChimp', 'getInterestsForCategory'],
            'edit',
            false,
            [
                'args' => [
                    'requestData' => [
                        'filter' => FILTER_DEFAULT,
                        'flags' => FILTER_REQUIRE_ARRAY
                    ]
                ]
            ]
        );
    }

    /**
     * Get controls for the builder
     * @return array
     */
    public function controls()
    {
        return [
            control(
                'api_key_input',
                'Use MailChimp API Key',
                [
                    'type' => 'api_key_input',
                    'layout' => 'vertical',
                    'apiKeyOptions' => [
                        'apiKeyName' => BREAKDANCE_MAILCHIMP_API_KEY_NAME
                    ]
                ]),

            control('audience', 'Audience', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'placeholder' => 'No audience selected',
                'dropdownOptions' => [
                    'populate' => [
                        'fetchDataAction' => 'breakdance_fetch_mailchimp_lists',
                        'fetchContextPath' => 'content.actions.mailchimp',
                        'refetchPaths' => ['content.actions.mailchimp.api_key_input'],
                    ],
                'noDataAvailableMessage' => 'Empty or invalid API key'
                ],
            ]),

            control('double_optin', 'Double Opt-In', [
                'type' => 'toggle',
                'condition' => ['path' => 'content.actions.mailchimp.audience', 'operand' => 'is set', 'value' => '']
            ]),

            controlSection('field_mapping', 'Field Mapping', [
                repeaterControl('fields', 'Fields', [
                    control('mailchimp_field', 'Mailchimp Field', [
                        'type' => 'dropdown',
                        'layout' => 'vertical',
                        'placeholder' => 'No field selected',
                        'dropdownOptions' => [
                            'populate' => [
                                'fetchDataAction' => 'breakdance_fetch_mailchimp_fields',
                                'fetchContextPath' => 'content.actions.mailchimp',
                                'refetchPaths' => ['content.actions.mailchimp.audience', 'content.actions.mailchimp.api_key_input'],
                            ],
                            'noDataAvailableMessage' => 'No fields available'
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
        ['condition' => [
            'path' => 'content.actions.mailchimp.audience',
            'operand' => 'is set', 'value' => '']
        ]),
        controlSection('interests', 'Audience Groups', [
            control('category', 'Category', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'placeholder' => 'Select Category',
                'dropdownOptions' => [
                    'populate' => [
                        'fetchDataAction' => 'breakdance_fetch_mailchimp_interest_categories',
                        'fetchContextPath' => 'content.actions.mailchimp',
                        'refetchPaths' => [
                            'content.actions.mailchimp.audience',
                            'content.actions.mailchimp.api_key_input'
                        ],
                    ],
                    'noDataAvailableMessage' => 'No audience groups available'
                ],
            ]),
            control('interest', 'Group', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'placeholder' => 'Select Audience Group',
                'dropdownOptions' => [
                    'populate' => [
                        'fetchDataAction' => 'breakdance_fetch_mailchimp_interests',
                        'fetchContextPath' => 'content.actions.mailchimp',
                        'refetchPaths' => [
                            'content.actions.mailchimp.audience',
                            '%%CURRENTPATH%%.category',
                            'content.actions.mailchimp.api_key_input'
                        ],
                    ],
                    'noDataAvailableMessage' => 'No audience groups available'
                ],
            ]),
        ],['condition' => [
            'path' => 'content.actions.mailchimp.audience',
            'operand' => 'is set', 'value' => ''
        ]])];
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        if (empty($this->apiKey)) {
            return '';
        }

        $server  = explode('-', $this->apiKey)[1] ?? false;

        if (empty($server)) {
            throw new \RuntimeException('The API key is must contain a data center');
        }

        return sprintf($this->baseUrl, $server);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Authorization' => "apikey {$this->apiKey}",
            'Accept'        => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Does something on form submission
     * @param FormData $form
     * @param FormSettings $settings
     * @param FormExtra $extra
     * @return ActionSuccess|ActionError|array<array-key, ActionSuccess|ActionError>
     */
    public function run($form, $settings, $extra)
    {
        $this->apiKey = self::getApiKeyFromApiKeyInput($settings['actions']['mailchimp']['api_key_input'], BREAKDANCE_MAILCHIMP_API_KEY_NAME);

        $isApiKeySetAndValid = self::isApiKeySet($this->apiKey);

        if ($isApiKeySetAndValid !== true) {
            return $isApiKeySetAndValid;
        }

        $mergeFields = \Breakdance\Forms\getMappedFieldValuesFromFormData(
            $settings['actions']['mailchimp']['field_mapping']['fields'],
            $form,
            'mailchimp_field'
        );

        $listId = $settings['actions']['mailchimp']['audience'];
        $optIn  = $settings['actions']['mailchimp']['double_optin'];
        $status = $optIn ? 'pending' : 'subscribed';

        /** @var ?string */
        $email  = $mergeFields['email'] ?? false;

        // don't send email with merge fields
        unset($mergeFields['email']);

        if (!$email) {
            return [
                'type'    => 'error',
                'message' => 'Email address is required.'
            ];
        }

        $requestData = [
            'status'        => $status,
            'email_address' => $email,
        ];

        if (!empty($mergeFields)) {
            $requestData['merge_fields'] = $mergeFields;
        }

        /** @var array{category: string, interest: string} $audienceGroup */
        $audienceGroup = $settings['actions']['mailchimp']['interests'] ?? [];
        if ($audienceGroup) {
            $requestData['interests'][$audienceGroup['interest']] = true;
        }

        $response = $this->request("lists/{$listId}/members", 'POST', json_encode($requestData));

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
            'type'     => 'success',
            'response' => $response
        ];
    }

    /**
     * @param ?string $apiKey
     * @return true|ActionError
     */
    public static function isApiKeySet($apiKey){
        if (!$apiKey || empty($apiKey)){
            return [
                'type'    => 'error',
                'message' => 'API key is not set.'
            ];
        }

        // valid keys look like abcdef-us14
        $dataCenter  = explode('-', $apiKey)[1] ?? false;

        if (empty($dataCenter)) {
            return [
                'type'    => 'error',
                'message' => 'Invalid API key. A valid key must have a data center associated with it'
            ];
        }

        return true;
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

        $response = (new self($apiKey))->request('ping');

        return self::getSuccessOrErrorFromApiKeyValidationResponse($response);
    }

    /**
     * @param FormRequestContext|null $requestData
     * @return DropdownData[]
     */
    public static function getLists($requestData)
    {
        if (empty($requestData) || !array_key_exists('context', $requestData) || !array_key_exists('api_key_input', $requestData['context'])) {
            return [];
        }

        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_MAILCHIMP_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }

        /** @var array{lists: array{name: string, id: string}[] | null} $response */
        $response = (new self($apiKey))->request('lists');

        if (array_key_exists('error', $response)) {
            return [];
        }

        return array_map(
            function ($list) {
                return [
                    'text' => $list['name'],
                    'value' => $list['id']
                ];
            },
            $response['lists'] ?? []
        );
    }

    /**
     * Fetch available merge fields for the given Mailchimp List
     * and return them in a format DropdownInput can use.
     *
     * @param FormRequestContext $requestData
     * @return DropdownData[]
     */
    public static function getFields($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_MAILCHIMP_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }


        /** @var ?string $listId */
        $listId = $requestData['context']['audience'] ?? null;
        if (!$listId) {
            return [];
        }

        /**
         * @var array{merge_fields: MailChimpField}[] $response
         */
        $response = (new self($apiKey))->request("lists/{$listId}/merge-fields");

        $fields =  array_map(
            function ($field) {
                return [
                    'text' => $field['name'],
                    'value' => $field['tag']
                ];
            },
            $response['merge_fields'] ?? []
        );
        $fields[] = [
            'text' => 'Email',
            'value' => 'email'
        ];
        return $fields;
    }

    /**
     * Fetch available interest categories for the given Mailchimp List
     * and return them in a format DropdownInput can use.
     *
     * @param FormRequestContextMailChimp $requestData
     * @return DropdownData[]
     */
    public static function getInterestCategories($requestData)
    {
        if (empty($requestData) || !array_key_exists('context', $requestData) || !array_key_exists('api_key_input', $requestData['context'])) {
            return [];
        }

        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_MAILCHIMP_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }


        /** @var ?string $listId */
        $listId = $requestData['context']['audience'] ?? null;
        if (!$listId) {
            return [];
        }

        /**
         * @var array{categories: MailChimpInterestCategory[], error: string} $response
         */
        $response = (new self($apiKey))->request("lists/{$listId}/interest-categories");

        if (array_key_exists('error', $response) || !array_key_exists('categories', $response)) {
            return [];
        }

        return array_map(
            function ($field) {
                return [
                    'text' => $field['title'],
                    'value' => $field['id']
                ];
            },
            $response['categories'] ?? []
        );
    }

    /**
     * @param FormRequestContextMailChimp $requestData
     * @return DropdownData[]
     */
    public static function getInterestsForCategory($requestData)
    {
        $apiKey = self::getApiKeyFromApiKeyInput($requestData['context']['api_key_input'], BREAKDANCE_MAILCHIMP_API_KEY_NAME);

        if (!$apiKey) {
            return [];
        }

        /** @var ?string $listId */
        $listId = $requestData['context']['audience'] ?? null;
        if (!$listId) {
            return [];
        }

        /** @var ?string $categoryId */
        $categoryId = $requestData['context']['interests']['category'] ?? null;
        if (!$categoryId) {
            return [];
        }

        $mailchimp = new self($apiKey);

        /**
         * @var array{interests: MailChimpInterest[], error: string} $response
         */
        $response = $mailchimp->request("lists/{$listId}/interest-categories/{$categoryId}/interests");

        if (array_key_exists('error', $response) || !array_key_exists('interests', $response)) {
            return [];
        }

        return array_map(
            function ($interest) {
                return [
                    'text' => $interest['name'],
                    'value' => $interest['id']
                ];
            },
            $response['interests'] ?? []
        );
    }

    /**
     * Handle The API Request Response.
     *
     * @param array|\WP_Error $response The response from the API request.
     * @psalm-suppress MixedInferredReturnType
     * @return array The response body or an error message.
     */
    protected function handleResponse($response)
    {
        if($response instanceof \WP_Error) {
            if ($response->get_error_code() === 'http_request_failed'){
                return ['error' => 'Error requesting data from MailChimp servers.'];
            }

            return ['error' => $response->get_error_message()];
        }

        if (wp_remote_retrieve_response_code($response) >= 300 ) {
            try {
                /** @var array $body */
                $body = json_decode(wp_remote_retrieve_body($response), true, 512, JSON_THROW_ON_ERROR);

                return ['error' => $body['detail'] ?? "Error getting data from MailChimp"];
            } catch (\JsonException $e) {
                return ['error' => wp_remote_retrieve_response_message($response)];
            }
        }

        return $this->getResponseBodyOrMessage($response);
    }

}
