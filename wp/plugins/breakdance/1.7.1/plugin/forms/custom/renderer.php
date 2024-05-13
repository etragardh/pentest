<?php

namespace Breakdance\Forms\Render;

use Breakdance\Forms\Actions\MailChimp;
use Breakdance\Render\ScriptAndStyleHolder;
use Breakdance\Render\Twig;
use Breakdance\Themeless\PopupController;
use function Breakdance\AJAX\get_nonce_for_ajax_requests;
use function Breakdance\APIKeys\getKey;
use function Breakdance\DynamicData\breakdanceDoShortcode;
use function Breakdance\Subscription\proOnlyFeaturesTheFormIsUsing;

/**
 * @param StoredFormField[] $fields
 * @param array{content: FormSettings, design: FormDesign, settings: array} $props
 * @param string $ajaxHandler
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 */
function renderForm($fields, $props, $ajaxHandler = 'empty')
{
    $form = $props['content'];
    $options = getFormOptions($ajaxHandler, $form);
    $formId = $form['advanced']['form_id'] ?? sanitize_title($form['form']['form_name'] ?? '') . '%%ID%%';
    $formLayout = $props['design']['layout']['layout'] ?? [];
    $hasBreakpoints = $props['design']['layout']['vertical_at'] ?? '';
    $honeypotEnabled = $options['honeypot_enabled'] ?? false;
    $csrfEnabled = $form['advanced']['csrf_enabled'] ?? false;

    // Buttons
    $submitButtonText = $form['form']['submit_text'] ?? 'Submit';
    $submitButtonId = $form['advanced']['submit_button_id'] ?? '';
    $submitButtonDesign = $props['design']['form']['submit_button']['styles'] ?? '';
    $uploadButtonDesign = $props['design']['form']['fields']['advanced']['file_input']['button'] ?? [];

    // Steps
    $steps = array_filter($fields, static function ($field) {
        return $field['type'] === 'step';
    });
    $stepCount = count($steps);

    $nextStepButtonDesign = $props['design']['form']['stepper']['next_button'] ?? [];
    $previousStepButtonDesign = $props['design']['form']['stepper']['previous_button'] ?? [];
    $nextStepButtonText = $form['form']['next_step_text'] ?? 'Next Step';
    $previousStepButtonText = $form['form']['previous_step_text'] ?? 'Previous Step';

    // Popups
    $successPopups = $form['actions']['popup']['popups_on_success'] ?? [];
    $errorPopups = $form['actions']['popup']['popups_on_error'] ?? [];
    $popups = array_merge($successPopups, $errorPopups);

    if (!empty($popups)) {
        foreach ($popups as $popup) {
            PopupController::getInstance()->registerPopup($popup['popup']);
        }
    }

    // Honeypot
    if ($honeypotEnabled) {
        /** @var StoredFormField $honeypotField */
        $honeypotField = [
            'type' => 'hpinput',
            'label' => 'HP Name',
            'advanced' => [
                'id' => 'hpname',
                'autocomplete_disabled' => true,
                'tabindex' => "-1",
            ],
        ];

        $fields[] = $honeypotField;
    }

    // CSRF
    if ($csrfEnabled) {
        /** @var StoredFormField $csrfField */
        $csrfField = [
            'type' => 'hidden',
            'name' => 'fields[csrfToken]',
            'advanced' => [
                'id' => 'csrf-token-%%ID%%',
                'value' => get_nonce_for_ajax_requests(),
                'autocomplete_disabled' => true,
                'tabindex' => "-1",
            ],
        ];

        $fields[] = $csrfField;
    }

    // Templates
    $renderedFields = renderFields($fields, $form);
    $formTemplate = getTemplate('form');
    $footerTemplate = getTemplate('footer');
    $stepperTemplate = getTemplate('stepper');

    $output = str_replace(
        ['%%STEPPER%%', '%%BODY%%', '%%FOOTER%%'],
        [$stepperTemplate, $renderedFields, $footerTemplate],
        $formTemplate
    );
    /**
     * @psalm-suppress TooManyArguments
     */
    $output = (string)apply_filters('breakdance_render_form_html', $output, $props, $fields);

    $rendered = Twig::getInstance()->runTwig($output, [
        'form' => $form,
        'options' => $options,
        'formId' => $formId,
        'formLayout' => $formLayout,
        'nextStepButtonText' => $nextStepButtonText,
        'nextStepButtonDesign' => $nextStepButtonDesign,
        'previousStepButtonText' => $previousStepButtonText,
        'previousStepButtonDesign' => $previousStepButtonDesign,
        'submitButtonText' => $submitButtonText,
        'submitButtonId' => $submitButtonId,
        'submitButtonDesign' => $submitButtonDesign,
        'uploadButtonDesign' => $uploadButtonDesign,
        'hasBreakpoints' => $hasBreakpoints,
        'steps' => $steps,
        'stepCount' => $stepCount,
        'disableSubmitButton' => proOnlyFeaturesTheFormIsUsing($props, $fields)
    ]);

    echo $rendered;
}

/**
 * @param string $ajaxHandler
 * @param FormSettings $settings
 * @return array
 */
function getFormOptions($ajaxHandler, $settings)
{
    $recaptchaEnabled = $settings['advanced']['recaptcha']['enabled'] ?? false;
    if ($recaptchaEnabled) {
        $recaptchaSiteKey = $settings['advanced']['recaptcha']['api_key_input']['apiUrl'] ?? getKey(BREAKDANCE_RECAPTCHA_SITE_KEY_NAME);

        ScriptAndStyleHolder::getInstance()->append([
            'scripts' => [
                'https://www.google.com/recaptcha/api.js?render=' . (string) $recaptchaSiteKey,
            ],
            'builderCondition' => "return false;",
        ]);
    }
    return [
        'slug' => $ajaxHandler,
        'name' => $settings['form']['form_name'] ?? '',
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'clearOnSuccess' => $settings['actions']['clear_on_success'] ?? true,
        'hideOnSuccess' => $settings['form']['hide_on_success'] ?? false,
        'successMessage' => $settings['form']['success_message'] ?? 'The form was submitted successfully.',
        'errorMessage' => $settings['form']['error_message'] ?? 'Something went wrong.',
        'redirect' => $settings['form']['redirect'] ?? false,
        'redirectUrl' => $settings['form']['redirect_url'] ?? '',
        'customJavaScript' => $settings['actions']['custom_javascript'] ?? '',
        'recaptcha' => [
            'key' => $recaptchaSiteKey ?? '',
            'enabled' => $settings['advanced']['recaptcha']['enabled'] ?? false,
        ],
        'honeypot_enabled' => $settings['advanced']['honeypot_enabled'] ?? false,
        'popupsOnSuccess' => $settings['actions']['popup']['popups_on_success'] ?? [],
        'popupsOnError' => $settings['actions']['popup']['popups_on_error'] ?? [],
    ];

}

/**
 * @param StoredFormField $field
 * @return ParsedFormField
 */
function getFieldAttributes($field)
{
    $label = $field['label'] ?? '';

    /**
     * @psalm-suppress MixedAssignment
     * @psalm-suppress InvalidArrayOffset
     * @psalm-suppress MixedArrayAccess
     */
    $id = \Breakdance\Forms\getIdFromField($field);

    $defaults = [
        'type' => 'text',
        'name' => "fields[{$id}]",
        'label' => $label,
        'placeholder' => '',
        'options' => [],
    ];

    // merge advanced defaults first
    $field['advanced'] = array_merge([
        'id' => $id,
        'required' => false,
    ], $field['advanced'] ?? []);

    return array_merge($defaults, $field);
}

/**
 * @param StoredFormField $stored
 * @param integer $step
 * @param FormSettings $form
 * @return string
 * @throws \Exception
 */
function renderField($stored, $step, $form)
{
    $field = getFieldAttributes($stored);
    $parentTemplate = getTemplate('field');

    // render dynamic property data in field value
    if (isset($field['advanced']['value']) && str_contains($field['advanced']['value'], '[breakdance_dynamic')) {
        $dynamicOutput = breakdanceDoShortcode($field['advanced']['value']);

        if (is_string($dynamicOutput)) {
            $field['advanced']['value'] = $dynamicOutput;
        }
    }

    $fieldTemplate = getTemplate("fields/{$field['type']}");
    $defaultTemplate = getTemplate('fields/input');

    $output = $fieldTemplate ?: $defaultTemplate;
    $output = str_replace('%%INPUT%%', $output, $parentTemplate);

    return Twig::getInstance()->runTwig($output, [
        'form' => $form,
        'field' => $field,
        'step' => $step,
    ]);
}

/**
 * @param StoredFormField[] $fields
 * @param FormSettings $form
 * @return string
 */
function renderFields($fields, $form)
{
    $step = 0;
    $rendered = [];
    foreach ($fields as $field) {

        if ($field['type'] === 'step') {
            $step += 1;
        } else {
            $rendered[] = renderField($field, $step, $form);
        }
    }
    return implode('', $rendered);
}

/**
 * @param string $name
 * @return string
 */
function getTemplate($name)
{
    /** @var array<string, string> $cache */
    static $cache = [];

    $file = __DIR__ . "/templates/{$name}.twig";

    if (!file_exists($file)) {
        return '';
    }

    if (!isset($cache[$name])) {
        $contents = file_get_contents($file);
        $cache[$name] = $contents ?: '';
    }

    return $cache[$name];
}
