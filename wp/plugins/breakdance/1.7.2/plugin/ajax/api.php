<?php

namespace Breakdance\AJAX;

use Exception;

use function Breakdance\Admin\is_breakdance_development_environment;

/**
 * @param string $route
 * @param callable $callback
 * @param string $minimum_permissions
 * @param bool $any_url
 * @param RegisterAjaxHandlerOptions $options
 * @param bool $skip_nonce_check
 * @return void
 * @throws Exception
 *
 * In case of renaming/moving to another namespace don't forget to adjust the readme.md file
 */
function register_handler($route, callable $callback, $minimum_permissions = 'never', $any_url = false, $options = [], $skip_nonce_check = false)
{

    HandlerNamesHolder::getInstance()->add($route);

    if ($minimum_permissions === 'never') {
        // bad request
        throw new Exception('You must specify the minimum permissions to access your AJAX action.', 500);
    }

    $action_name = 'wp_ajax_' . $route;
    $any_action_name = 'breakdance_ajax_' . $route;

    $handler = function () use ($callback, $any_url, $minimum_permissions, $route, $options, $skip_nonce_check) {
        try {
            ob_start();

            $isHandlerForRemoteRequest = !empty($options['remote']) && $options['remote'];
            $allowed = $isHandlerForRemoteRequest ? true : \Breakdance\Permissions\hasMinimumPermission($minimum_permissions);

            if (!$allowed) {
                if (!is_user_logged_in()) {
                    throw new BreakdanceAjaxHandlerException(
                        "You need to be logged in and have minimum permission level of \"{$minimum_permissions}\"", 401
                    );
                }

                // forbidden request
                $error = sprintf(
                    "Not enough permissions to perform the action \"%s\", minimum permission level is \"%s\".",
                    $route,
                    $minimum_permissions
                );
                throw new BreakdanceAjaxHandlerException($error, 403);
            }

            if (!$isHandlerForRemoteRequest && !$skip_nonce_check) {
                $nonceTick = check_ajax_referer(get_nonce_key_for_ajax_requests(), false, !$isHandlerForRemoteRequest);
                if (!$nonceTick) {
                    throw new BreakdanceAjaxHandlerException(
                        "Failed security check", 419, null, 'Expired'
                    );
                }

                if ($nonceTick === 2) {
                    $refreshNonce = get_nonce_for_ajax_requests();
                    header('Breakdance-Refresh-Nonce:' . $refreshNonce);
                }
            }

            $callback_args = [];
            if (!empty($options['args'])) {
                /** @var null|false|array<string, string|int|null|bool|array> $callback_args_filtered */
                $callback_args_filtered = filter_input_array(INPUT_POST, $options['args'], true);

                if ($callback_args_filtered === false || $callback_args_filtered === null) {
                    $error_message = !is_breakdance_development_environment()
                        ? 'Bad request'
                        : 'All of the required POST parameters are missing';

                    throw new BreakdanceAjaxHandlerException($error_message, 400);
                }

                $erroneous_arg_names = [];
                foreach ($callback_args_filtered as $arg_name => $arg_value) {
                    $is_optional_arg =
                        isset($options['optional_args']) &&
                        in_array($arg_name, $options['optional_args']);

                    if ($arg_value === false || ($arg_value === null && !$is_optional_arg)) {
                        $erroneous_arg_names[] = $arg_name;
                    }
                }

                if (sizeof($erroneous_arg_names)) {
                    $error_message = !is_breakdance_development_environment()
                        ? 'Bad request'
                        : sprintf(
                            'Required POST parameters are missing or invalid: "%s"',
                            implode('", "', $erroneous_arg_names)
                        );
                    throw new BreakdanceAjaxHandlerException($error_message, 400);
                }

                $callback_args = $callback_args_filtered;
            }

            try {
                /** @var mixed $return_value */
                /** @psalm-suppress MixedAssignment */
                $return_value = call_user_func_array($callback, array_values($callback_args));
            } catch (Exception $callback_exec_exception) {
                throw new BreakdanceAjaxHandlerException($callback_exec_exception->getMessage(), 500, $callback_exec_exception);
            }

            if (is_array($return_value) && array_key_exists('error', $return_value)) {
                throw new BreakdanceAjaxHandlerException((string) $return_value['error'], 500);
            } elseif (!is_array($return_value) && !$return_value) {
                $return_value = (object) [];
            } elseif (!is_array($return_value)) {
                $error = sprintf(
                    "AJAX callbacks should return an associative array ([\"key\" => \"value\") or a blank array ([]). Got %s for route \"%s\"",
                    print_r($return_value, true),
                    $route
                );

                throw new BreakdanceAjaxHandlerException($error, 500);
            }

            $unexpected_output = ob_get_clean();

            if ($unexpected_output) {
                $error = 'Unexpected output during AJAX request. AJAX Callbacks should return an associative array. --------- ' . wp_strip_all_tags(
                        $unexpected_output
                    );
                // bad request
                throw new BreakdanceAjaxHandlerException($error, 500);
            } else {
                wp_send_json($return_value, 200);
            }
        } catch (BreakdanceAjaxHandlerException $e) {
            if (ob_get_level()) {
                ob_end_clean();
            }

            throw $e;
        }
    };

    add_action($action_name, $handler);
    add_action('wp_ajax_nopriv_' . $route, $handler);

    if ($any_url) {
        add_action($any_action_name, $handler);
    }
}


/**
 * @return string
 */
function get_nonce_key_for_ajax_requests() {
    return 'breakdance_ajax';
}

/**
 * @return string
 */
function get_nonce_for_ajax_requests() {
    return wp_create_nonce(get_nonce_key_for_ajax_requests());
}

add_action('wp_login', /**
 * @param string $userLogin
 * @return void
 */ static function($userLogin) {
    $transient = 'breakdance_nonce_updated_' . $userLogin;
    set_transient($transient, 1, 0);
});

/**
 * Broadcast updated nonce to the builder on login
 *  or if breakdanceRefreshSession parameter is set
 *
 * @return void
 */
function broadcastNonce() {
    if (!is_user_logged_in()) {
        return;
    }

    if (!\Breakdance\Permissions\hasPermission('full')) {
        return;
    }

    $currentUser = wp_get_current_user();
    $transient = 'breakdance_nonce_updated_' . $currentUser->user_login;
    /** @var int|false $hasRefreshNonceTransient */
    $hasRefreshNonceTransient = get_transient($transient);

    $hasRefreshNonceQueryParam = (boolean) filter_input(INPUT_GET, 'breakdanceRefreshSession', FILTER_VALIDATE_BOOLEAN);

    if ($hasRefreshNonceQueryParam || $hasRefreshNonceTransient) {
        $nonce = \Breakdance\AJAX\get_nonce_for_ajax_requests();
        $script = <<<JS
            const broadcastChannel = new BroadcastChannel('breakdance');
            broadcastChannel.postMessage({event: 'nonceRefresh', nonce: '$nonce'});
            broadcastChannel.close();
JS;
        wp_register_script('nonceRefreshBroadcast', '');
        wp_enqueue_script('nonceRefreshBroadcast');
        wp_add_inline_script('nonceRefreshBroadcast', $script, 'before');
        delete_transient($transient);
        if ($hasRefreshNonceQueryParam) {
            add_action('admin_notices', static function() {
                echo '<div class="updated"><p>Your Breakdance session token has been refreshed</p></div>';
            });
        }
    }
}
add_action('admin_enqueue_scripts', '\Breakdance\AJAX\broadcastNonce');



class BreakdanceAjaxHandlerException extends \Exception
{
    /**
     * @var string
     */
    protected $statusDescription = '';

    /**
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param string $description
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null, $description = '')
    {
        $this->statusDescription = $description;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getStatusDescription()
    {
        return $this->statusDescription;
    }
}

class HandlerNamesHolder
{

    use \Breakdance\Singleton;

    /**
     * @var string[]
     */
    public $handlerNames = [];

    /**
     * @param string $name
     * @return void
     */
    public function add($name)
    {
        $this->handlerNames[] = $name;
    }

}
