<?php

namespace Breakdance\Forms;

/**
 * @return FormFieldType[]
 */
function fieldTypes()
{
    return [
        [
            'label' => 'Text',
            'slug' => 'text',
            'handler' => __NAMESPACE__ . '\textHandler',
        ],
        [
            'label' => 'Email',
            'slug' => 'email',
            'handler' => __NAMESPACE__ . '\emailHandler',
        ],
        [
            'label' => 'Textarea',
            'slug' => 'textarea',
            'handler' => __NAMESPACE__ . '\textareaHandler',
        ],
        [
            'label' => 'URL',
            'slug' => 'url',
            'handler' => __NAMESPACE__ . '\textHandler',
        ],
        [
            'label' => 'Phone Number',
            'slug' => 'tel',
            'handler' => __NAMESPACE__ . '\textHandler',
        ],
        [
            'label' => 'Radio',
            'slug' => 'radio',
            'handler' => __NAMESPACE__ . '\radioHandler',
        ],
        [
            'label' => 'Checkbox',
            'slug' => 'checkbox',
            'handler' => __NAMESPACE__ . '\checkboxHandler',
        ],
        [
            'label' => 'Select',
            'slug' => 'select',
            'handler' => __NAMESPACE__ . '\selectHandler',
        ],
        [
            'label' => 'Number',
            'slug' => 'number',
            'handler' => __NAMESPACE__ . '\numberHandler',
        ],
        [
            'label' => 'Date',
            'slug' => 'date',
            'handler' => __NAMESPACE__ . '\dateHandler',
        ],
        [
            'label' => 'Time',
            'slug' => 'time',
            'handler' => __NAMESPACE__ . '\timeHandler',
        ],
         [
             'label' => 'File Upload',
             'slug' => 'file',
             'handler' => __NAMESPACE__ . '\voidHandler',
             'proOnly' => true
         ],
        [
            'label' => 'Password',
            'slug' => 'password',
            'handler' => __NAMESPACE__ . '\textHandler',
        ],
        [
            'label' => 'Hidden',
            'slug' => 'hidden',
            'handler' => __NAMESPACE__ . '\textHandler',
        ],
        [
            'label' => 'HTML',
            'slug' => 'html',
            'handler' => __NAMESPACE__ . '\voidHandler',
        ],
        [
            'label' => 'Step',
            'slug' => 'step',
            'handler' => __NAMESPACE__ . '\voidHandler',
            'proOnly' => true,
        ],
    ];
}

/**
 * @param StoredFormField $field
 * @param string | string[] $value
 * @return string
 */
function getSubmissionPanelFieldHandler($field, $value)
{
    $fields = fieldTypes();
    $fieldIndex = array_search($field['type'], array_column($fields, 'slug'), true);
    if ($fieldIndex !== false) {
        $handlerFunction = $fields[$fieldIndex]['handler'];
        if (is_callable($handlerFunction)) {
            /** @var string $handlerOutput */
            $handlerOutput = $handlerFunction($field, $value);
            return $handlerOutput;
        }
    }
    trigger_error('No field handler defined for field with ID ' . $field['advanced']['id'], E_USER_NOTICE);
    return '';
}

/**
 * @param StoredFormField $field
 * @param string $value
 * @param FormInputType $type
 * @return string
 */
function textHandler($field, $value, $type = 'text')
{
    return sprintf('<input class="widefat" type="%s" name="fields[%s]" value="%s" id="%s">', $type, $field['advanced']['id'], esc_attr($value), $field['advanced']['id']);
}

/**
 * @param StoredFormField $field
 * @param string $value
 * @return string
 */
function numberHandler($field, $value)
{
    return textHandler($field, $value, 'number');
}

/**
 * @param StoredFormField $field
 * @param string $value
 * @return string
 */
function dateHandler($field, $value)
{
    return textHandler($field, $value, 'date');
}

/**
 * @param StoredFormField $field
 * @param string $value
 * @return string
 */
function timeHandler($field, $value)
{
    return textHandler($field, $value, 'time');
}

/**
 * @param StoredFormField $field
 * @param string $value
 * @return string
 */
function emailHandler($field, $value)
{
    return sprintf('<input class="widefat" type="%s" name="fields[%s]" value="%s" id="%s">', 'email', $field['advanced']['id'], esc_attr((string) $value), $field['advanced']['id']);
}

/**
 * @param StoredFormField $field
 * @param string $value
 * @return string
 */
function textareaHandler($field, $value)
{
    return sprintf('<textarea class="widefat" type="text" name="fields[%s]" id="%s">%s</textarea>', $field['advanced']['id'], $field['advanced']['id'], esc_html($value));
}

/**
 * @param StoredFormField $field
 * @param string|string[] $value
 * @return string
 */
function radioHandler($field, $value)
{
    $options = array_map(static function ($option) use ($field, $value) {
        $optionValue = $option['value'] ?? $option['label'];
        $checked = $optionValue === $value ? 'checked' : '';
        if (is_array($value)) {
            $checked = in_array($optionValue, $value, true) ? 'checked' : '';
        }
        return sprintf('<input type="radio" name="fields[%s]" value="%s" id="%s" %s><label class="selectit">%s</label>', $field['advanced']['id'], htmlentities($optionValue), $field['advanced']['id'], $checked, $option['label']);
    }, $field['options'] ?? []);
    return implode("<br>", $options);
}

/**
 * @param StoredFormField $field
 * @param string|string[] $value
 * @return string
 */
function checkboxHandler($field, $value)
{
    $options = array_map(static function ($option) use ($field, $value) {
        $optionValue = $option['value'] ?? $option['label'];
        $checked = $optionValue === $value ? 'checked' : '';
        if (is_array($value)) {
            $checked = in_array($optionValue, $value, true) ? 'checked' : '';
        }
        return sprintf('<input type="checkbox" name="fields[%s][]" value="%s" id="%s" %s><label class="selectit">%s</label>', $field['advanced']['id'], htmlentities($optionValue), $field['advanced']['id'], $checked, $option['label']);
    }, $field['options'] ?? []);
    return implode("<br>", $options);
}

/**
 * @param StoredFormField $field
 * @param string|string[] $value
 * @return string
 */
function selectHandler($field, $value)
{
    $optionsWithDefaultOption = array_merge([[
        'label' => 'Select...',
        'value' => '',
    ]], $field['options'] ?? []);

    $options = implode("\n", array_map(static function ($option) use ($field, $value) {
        $optionValue = $option['value'] ?? $option['label'];
        $selected = $optionValue === $value ? 'selected' : '';
        if (is_array($value)) {
            $selected = in_array($optionValue, $value, true) ? 'selected' : '';
        }
        return sprintf('<option value="%s" %s>%s</option>', htmlentities($optionValue), $selected, $option['label']);
    }, $optionsWithDefaultOption));

    $multiple = isset($field['multiple']) ? 'multiple' : '';

    $name = "fields[{$field['advanced']['id']}]";
    if ($multiple) {
        $name = "fields[{$field['advanced']['id']}][]";
    }

    return sprintf('<select name="%s" id="%s" %s>%s</select>', $name, getIdFromField($field), $multiple, $options);
}

/**
 * @return string
 */
function voidHandler()
{
    return '';
}
