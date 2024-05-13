<?php
namespace Breakdance\APIKeys;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler('breakdance_validate_api_key', 'Breakdance\APIKeys\validate_api_key', 'edit');

    \Breakdance\Forms\Actions\MailChimp::registerAjaxHandlers();
    \Breakdance\Forms\Actions\ConvertKit::registerAjaxHandlers();
    \Breakdance\Forms\Actions\MailerLite::registerAjaxHandlers();
    \Breakdance\Forms\Actions\GetResponse::registerAjaxHandlers();
    \Breakdance\Forms\Actions\Drip::registerAjaxHandlers();
    \Breakdance\Forms\Actions\ActiveCampaign::registerAjaxHandlers();
});

/**
 * @return array{data: ActionSuccess|ActionError}|array{error: string}
 */
function validate_api_key(){
    $apiKeyName = (string) filter_input(INPUT_POST, 'apiKeyName');
    $apiKey = (string) filter_input(INPUT_POST, 'apiKey');
    $apiUrl = (string) filter_input(INPUT_POST, 'apiUrl');

    $response = "";

    switch ($apiKeyName){
        case BREAKDANCE_DISCORD_WEBHOOK_URL_NAME:
            $response = \Breakdance\Forms\Actions\Discord::validateApiKey($apiKey);
            break;
        case BREAKDANCE_SLACK_WEBHOOK_URL_NAME:
            $response = \Breakdance\Forms\Actions\Slack::validateApiKey($apiKey);
            break;
        case BREAKDANCE_MAILCHIMP_API_KEY_NAME:
            $response = \Breakdance\Forms\Actions\MailChimp::validateApiKey($apiKey);
            break;
        case BREAKDANCE_CONVERTKIT_API_KEY_NAME:
            $response = \Breakdance\Forms\Actions\ConvertKit::validateApiKey($apiKey);
            break;
        case BREAKDANCE_GETRESPONSE_API_KEY_NAME:
            $response = \Breakdance\Forms\Actions\GetResponse::validateApiKey($apiKey);
            break;
        case BREAKDANCE_MAILERLITE_API_KEY_NAME:
            $response = \Breakdance\Forms\Actions\MailerLite::validateApiKey($apiKey);
            break;
        case BREAKDANCE_DRIP_API_KEY_NAME:
            $response = \Breakdance\Forms\Actions\Drip::validateApiKey($apiKey);
            break;
        case BREAKDANCE_ACTIVECAMPAIGN_API_KEY_NAME:
            $response = \Breakdance\Forms\Actions\ActiveCampaign::validateApiKey(['apiKey' => $apiKey, 'apiUrl' => $apiUrl]);
            break;
        case BREAKDANCE_RECAPTCHA_SECRET_KEY_NAME:
            $response = \Breakdance\Forms\Recaptcha\validateRecaptchaKeys(['apiKey' => $apiKey, 'apiUrl' => $apiUrl]);
            break;
    }

    if (!$response){
        return ['error' => "No validation happened, likely because the API key name wasn't specified"];
    }

    return ['data' => $response];
}
