<?php

namespace Breakdance\Forms\Actions;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;

class CustomJavaScript extends Action {

    /**
     * Get the displayable label of the action.
     * @return string
     */
    public static function name()
    {
        return 'Custom JavaScript';
    }

    /**
     * Get the URL friendly slug of the action.
     * @return string
     */
    public static function slug()
    {
        return 'custom_javascript';
    }

    /**
     * Get controls for the builder
     * @return array
     */
    public function controls()
    {
        return [
            control('js_on_success', 'Run JS on Successful Submission', [
                'type' => 'code',
                'layout' => 'vertical',
                'placeholder' => 'console.log(formValues);',
                'codeOptions' => ['language' => 'javascript']
            ]),

            control('js_on_error', 'Run JS on Failed Submission', [
                'type' => 'code',
                'layout' => 'vertical',
                'placeholder' => 'console.log(formValues);',
                'codeOptions' => ['language' => 'javascript']
            ]),
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
        // Do nothing on purpose, log the JS that was executed
        $this->addContext('JavaScript', $settings['actions']['custom_javascript']);
        return ['type' => 'success'];
    }

}
