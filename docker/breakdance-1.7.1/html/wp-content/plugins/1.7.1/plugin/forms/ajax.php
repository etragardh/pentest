<?php

namespace Breakdance\Forms;

class AjaxFormsController {

    use \Breakdance\Singleton;

    /**
     * @var AjaxForm[]
     */
    private $forms = [];
    /**
     * @var string
     */
    private $prefix = 'breakdance_form_';

    /**
     * Register an ajax form handler.
     * @param AjaxForm $form
     */
    public function register($form)
    {
        $action = $this->prefix . $form['slug'];
        $permission = array_key_exists('loggedIn', $form) && $form['loggedIn'] ? 'edit' : 'none';


        $handler =
            /**
             * @param array ...$args
             * @return void
             */
            function (...$args) use ($form) {
                try {
                    /** @var FormSuccess|FormError $output */
                    $output = $form['handler'](...$args);

                    if (isAjaxError($output)) {
                        wp_send_json_error($output, 400);
                    } else {
                        wp_send_json_success($output);
                    }
                } catch (\Throwable $e) {
                    if (current_user_can('edit_pages')) {
                        wp_send_json_error([
                            'message' => 'Admin-only: ' . $e->getMessage(),
                            'response' => $e->getTrace()
                        ], 400);
                    } else {
                        wp_send_json_error('An unexpected error occurred.', 400);
                    }
                }
            };
        \Breakdance\AJAX\register_handler($action, $handler, $permission, false, [
            'args' => $form['args'] ?? [],
            'optional_args' => $form['optional_args'] ?? [],
        ], true);

        $this->forms[] = $form;
    }

}

/**
 * @param FormSuccess|FormError|array<array-key, FormSuccess|FormError> $maybeErrors
 * @return bool
 */
function isAjaxError($maybeErrors) {
    if (isset($maybeErrors['type']) && $maybeErrors['type'] === 'error') {
        return true;
    }

    $found = array_search('error', array_column($maybeErrors, 'type'));
    return $found !== false;
}

/**
 * Register an ajax form handler.
 * @param AjaxForm $form
 */
function registerForm($form)
{
    AjaxFormsController::getInstance()->register($form);
}
