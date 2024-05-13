<?php

namespace Breakdance\Forms\Actions;

use Requests_Utility_CaseInsensitiveDictionary;
use function Breakdance\Elements\control;
use function Breakdance\Elements\repeaterControl;

class Webhook extends Action {

    /**
     * Get the displayable label of the action.
     * @return string
     */
    public static function name()
    {
        return 'Webhook';
    }

    /**
     * Get the URL friendly slug of the action.
     * @return string
     */
    public static function slug()
    {
        return 'webhook';
    }

    /**
     * Get controls for the builder
     * @return array
     */
    public function controls()
    {
        return [
            control('webhook_url', 'Webhook URL', [
                'type' => 'text',
                'layout' => 'vertical'
            ]),
            repeaterControl('webhook_field_map', 'Field Map',
                [
                    control('name', 'Field Name', [
                        'type' => 'text',
                        'layout' => 'vertical'
                    ]),
                    control('value', 'Field Value', [
                        'type' => 'text',
                        'layout' => 'vertical',
                        'variableOptions' => [
                            'enabled' => true,
                            'populate' => [
                                'path' => 'content.form.fields',
                                'text' => 'label',
                                'value' => 'advanced.id'
                            ]
                        ]
                    ]),
                ],
                [
                    'repeaterOptions' => [
                        'titleTemplate' => '{name}',
                        'defaultTitle' => 'Data',
                        'buttonName' => 'Add data'
                    ]
                ]
            ),
            repeaterControl('webhook_headers', 'Headers',
                [
                    control('name', 'Header Name', [
                        'type' => 'text',
                        'layout' => 'vertical'
                    ]),
                    control('value', 'Header Value', [
                        'type' => 'text',
                        'layout' => 'vertical',
                        'variableOptions' => [
                            'enabled' => true,
                            'populate' => [
                                'path' => 'content.form.fields',
                                'text' => 'label',
                                'value' => 'advanced.id'
                            ]
                        ]
                    ]),
                ],
                [
                    'repeaterOptions' => [
                        'titleTemplate' => '{name}',
                        'defaultTitle' => 'Headers',
                        'buttonName' => 'Add header'
                    ]
                ]
            ),
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
        $url = $settings['actions']['webhook']['webhook_url'];

        $body = [];
        $fieldMap = $settings['actions']['webhook']['webhook_field_map'];
        if (empty($fieldMap)) {
            $body = $extra;
        }

        $arrayVariables = $this->mapArrayFieldsToVariables($extra['fields']);
        $stringVariables = $this->mapStringFieldsToVariables($extra['fields']);
        $fieldVariableKeys = array_keys($stringVariables);
        /** @var array<string, string> $fieldVariableValues */
        $fieldVariableValues = array_values($stringVariables);

        foreach ($fieldMap as $field) {
            $body[$field['name']] = str_replace($fieldVariableKeys, $fieldVariableValues, $field['value']);
            if (array_key_exists(trim($field['value']), $arrayVariables)) {
                $body[$field['name']] = $arrayVariables[trim($field['value'])];
            }
        }

        $headers = [];
        $headerConfig = $settings['actions']['webhook']['webhook_headers'];
        foreach ($headerConfig as $header) {
            $headers[$header['name']] = str_replace($fieldVariableKeys, $fieldVariableValues, $header['value'], );
        }


        $response = wp_remote_post($url, [
            'body' => $body,
            'headers' => $headers,
        ]);

        $responseHeaders = wp_remote_retrieve_headers($response);
        // wp_remote_retrieve_headers can return an empty array or
        // a Requests_Utility_CaseInsensitiveDictionary object
        // so let's check for this and convert it to an array
        if ($responseHeaders instanceof Requests_Utility_CaseInsensitiveDictionary) {
            $responseHeaders = $responseHeaders->getAll();
        }

        $this->addContext('Request Headers', $headers);
        $this->addContext('Request Body', $body);
        if (!empty($response)) {
            $this->addContext('Response Headers', $responseHeaders);
        }

        if ($response instanceof \WP_Error) {
            $this->addContext('Response Body', [
                'message' => $response->get_error_message(),
                'data' => $response->get_error_data()
            ]);
            /** @psalm-suppress PossiblyInvalidMethodCall */
            return [
                'type' => 'error',
                'message' => $response->get_error_message()
            ];
        }

        $this->addContext('Response Body', \Breakdance\Forms\jsonDecodeIfValidJson($response));

        return ['type' => 'success'];
    }

    /**
     * Returns fields with field names in variable syntax
     *  for string replacing i.e "{key}" => $value
     *
     * @param FormUserSubmittedContents $fields
     * @return array<string, array<array-key, mixed>|string>
     */
    private function mapStringFieldsToVariables($fields) {
        $fieldVariables = [];
        foreach ($fields as $fieldKey => $fieldValue) {
            $fieldVariableKey = sprintf('{%s}', $fieldKey);
            if (is_string($fieldValue)) {
                $fieldVariables[$fieldVariableKey] = $fieldValue;
            }
        }

        return $fieldVariables;
    }


    /**
     * Returns array fields with field names as keys
     *
     * @param FormUserSubmittedContents $fields
     * @return array<string, array<array-key, mixed>|string>
     */
    private function mapArrayFieldsToVariables($fields) {
        $arrayVariables = [];
        foreach ($fields as $fieldKey => $fieldValue) {
            if (is_array($fieldValue)) {
                $fieldVariableKey = sprintf('{%s}', $fieldKey);
                $arrayVariables[$fieldVariableKey] = $fieldValue;
            }
        }

        return $arrayVariables;
    }
}
