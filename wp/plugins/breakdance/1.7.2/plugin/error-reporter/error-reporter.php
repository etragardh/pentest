<?php

namespace Breakdance\ErrorReporter;

use Breakdance\AJAX\HandlerNamesHolder;
use Breakdance\Lib\Vendor\Whoops\Exception\Formatter;
use Breakdance\Lib\Vendor\Whoops\Run;

if (isBreakdanceAjaxRequestOrSomeoneIsSpoofingIt()) {
    registerCustomErrorHandling();
}

/**
 * @return void
 */
function registerCustomErrorHandling()
{
    overrideWpDieAjaxHandler();
    registerWhoopsErrorHandlers();
}

/**
 * @return void
 */
function registerWhoopsErrorHandlers()
{
    $runner = new Run();
    $runner->pushHandler(new AjaxHandler());


    $cb =
        /**
         * @param \Throwable $exception
         * @param \Breakdance\Lib\Vendor\Whoops\Exception\Inspector $inspector
         * @param \Breakdance\Lib\Vendor\Whoops\RunInterface $run
         * @return void
         */
        function ($exception, $inspector, $run) {
            /** @var string $log_entry */
            $log_entry = Formatter::formatExceptionPlain($inspector);

            // Remove all control chars except for newline
            $log_entry = str_replace(
                array_map(function ($chrIdx) {
                    return chr($chrIdx);
                }, array_merge(range(0, 9), range(11, 31))),
                '',
                $log_entry
            );

            error_log($log_entry);
        };
    $runner->pushHandler($cb);
    $runner->register();
}

/**
 * @return void
 */
function overrideWpDieAjaxHandler()
{

    /**
     * SECURITY - CRITICAL
     * WordPress core and other plugins expect execution to stop after calling wp_die()
     * By overriding the die handler and allowing execution to continue, we open up security holes
     * Any attacker could spoof the header "X-Requested-With: breakdancexmlhttprquest" even if they
     * were hitting a non-Breakdance AJAX handler
     * 
     * Therefore, we have this check to only override the wp_die if the AJAX request
     * is actually a Breakdance AJAX request
     * 
     * https://github.com/soflyy/breakdance/issues/4178
     */

    add_filter('wp_die_ajax_handler',
        /**
         * @param mixed $originalHandler
         * @return mixed
         */
        function ($originalHandler) {
            
            /** @var string|false */
            $action = $_REQUEST['action'] ?? false;

            $isActuallyABreakdanceAjaxRequest = $action && in_array($action, HandlerNamesHolder::getInstance()->handlerNames);

            if ($isActuallyABreakdanceAjaxRequest) {
                return '\Breakdance\ErrorReporter\wpDieAjaxHandlerReplacement';
            } else {
                return $originalHandler;
            }

        },
    PHP_INT_MAX);
}

/**
 * @return boolean
 */
function isBreakdanceAjaxRequestOrSomeoneIsSpoofingIt()
{
    return array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) &&
        strtolower(
            (string)$_SERVER['HTTP_X_REQUESTED_WITH']
        ) === 'breakdancexmlhttprequest';
}

/**
 * We should literally do nothing, because everything will be picked up by Whoops' handlers
 *
 * @return void
 */
function wpDieAjaxHandlerReplacement()
{
}
