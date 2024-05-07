<?php

namespace Breakdance\Forms\Actions;

abstract class ApiAction extends Action
{

    /**
     * Makes an API request.
     *
     * @param string $endpoint The endpoint to request.
     * @param string $method The HTTP method to use for the request.
     * @param string|array $data The data to send with the request.
     * @param array $options Optional request options to be passed to wp_remote_request
     * @return array
     */
    protected function request($endpoint, $method = 'GET', $data = null, $options = [])
    {
        try {
            $url = $this->getBaseUrl() . $endpoint;
            $params = $this->getQueryParams();
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }

            $args = array_merge(
                ['method' => $method],
                ['headers' => $this->getHeaders()],
                ['body' => $data],
                $options,
            );

        } catch (\Exception $e) {
            return ['error' => $e];
        }

        $response = wp_remote_request($url, $args);
        $responseHeaders = wp_remote_retrieve_headers($response);
        if ($responseHeaders instanceof \Requests_Utility_CaseInsensitiveDictionary) {
            $responseHeaders = $responseHeaders->getAll();
        }

        if ($response instanceof \WP_Error) {

            $responseBody = [
                'message' => $response->get_error_message(),
                'data' => $response->get_error_data(),
            ];
        } else {
            $responseBody = $this->getResponseBodyOrMessage($response);
        }

        $this->addContext('Request Headers', $this->getHeaders());
        $this->addContext('Request Body', \Breakdance\Forms\jsonDecodeIfValidJson($data));
        $this->addContext('Response Headers', $responseHeaders);
        $this->addContext('Response Body', $responseBody);

        /**
         * @psalm-suppress MixedInferredReturnType
         */
        return $this->handleResponse($response);
    }

    /**
     * Handle The API Request Response.
     *
     * @param array|\WP_Error $response The response from the API request.
     * @psalm-suppress MixedInferredReturnType
     * @return array The response body, or message, or an error message.
     */
    protected function handleResponse($response)
    {
        if ($response instanceof \WP_Error) {
            if ($response->get_error_code() === 'http_request_failed'){
                return ['error' => 'Error requesting data. The service may be down, your settings may be wrong, or there\'s a problem with your server'];
            }

            return ['error' => $response->get_error_message()];
        }

        $responseData = $this->getResponseBodyOrMessage($response);

        if (wp_remote_retrieve_response_code($response) >= 300) {
            return ['error' => $responseData['message'] ?? 'Error accessing resource'];
        }

        return $responseData;
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     * @param array $response
     * @return array|array{error: string}
     */
    protected function getResponseBodyOrMessage($response)
    {
        $maybeBody =  wp_remote_retrieve_body($response);
        $maybeMessage = wp_remote_retrieve_response_message($response);

        try {
            if ($maybeBody) {
                /**
                 * @psalm-suppress MixedReturnStatement
                 */
                return json_decode($maybeBody, true, 512, JSON_THROW_ON_ERROR);
            } elseif($maybeMessage){
                return ['message' => $maybeMessage];
            } else {
                throw new \Exception();
            }
        } catch (\JsonException $e) {
            if ($maybeMessage){
                return ['message' => $maybeMessage];
            }

            return ['error' => 'Invalid response'];
        }
    }

    /**
     * Returns the API Request Headers.
     *
     * @return array The request headers
     */
    public function getHeaders()
    {
        return [];
    }

    /**
     * Returns the API Request Headers.
     *
     * @return array The request headers
     */
    public function getQueryParams()
    {
        return [];
    }

    /**
     * Returns the API Base URL.
     *
     * @return string The API key.
     */
    public function getBaseUrl()
    {
        return '';
    }

    /**
     * @param ?string $apiKey
     * @return true|ActionError
     */
    public static function isApiKeySet($apiKey)
    {
        if (!$apiKey || empty($apiKey)) {
            return [
                'type' => 'error',
                'message' => 'API key is not set.'
            ];
        }

        return true;
    }

    /**
     * @param ApiKeyInput|null $apiKeyInput
     * @param string $apiKeyName
     * @return string|null
     */
    public static function getApiKeyFromApiKeyInput($apiKeyInput, $apiKeyName)
    {
        if (!$apiKeyInput) {
            return \Breakdance\APIKeys\getKey($apiKeyName);
        }

        $apiKey = null;

        if ($apiKeyInput['type'] === 'default') {
            $apiKey = \Breakdance\APIKeys\getKey($apiKeyName);
        } else {
            $apiKey = $apiKeyInput['apiKey'];
        }

        if (!$apiKey || self::isApiKeySet($apiKey) !== true) {
            return null;
        }

        return trim($apiKey);
    }

    /**
     * @param array $response
     * @param string $serviceName
     * @return ActionSuccess|ActionError
     */
    protected static function getSuccessOrErrorFromApiKeyValidationResponse($response){
        if (array_key_exists('error', $response)) {
            /** @var string $error */
            $error = $response['error'];
            return [
                'type'    => 'error',
                'message' => $error
            ];
        }

        return [
            'type'    => 'success',
            'message' => 'API Key is valid.'
        ];
    }
}
