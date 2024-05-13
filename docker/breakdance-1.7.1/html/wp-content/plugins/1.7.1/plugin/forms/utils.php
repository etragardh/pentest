<?php

namespace Breakdance\Forms;

/**
 * @param array|null $fieldsRepeater
 * @param FormData $form
 * @param string $fieldControlSlug
 * @return array<array-key, mixed|string>
 */
function getMappedFieldValuesFromFormData($fieldsRepeater, $form, $fieldControlSlug)
{
    if (!$fieldsRepeater) return [];

    $fields = [];
    foreach ($form as $field) {
        $fields[$field['advanced']['id']] = $field;
    }

    return array_reduce($fieldsRepeater,
        /**
         * @param array $carry
         * @param array $fieldValue
         */
        function($carry, $fieldValue) use ($fields, $fieldControlSlug) {
        /** @var string $fieldId */
        $fieldId = $fieldValue['formField'] ?? null;
        /** @var string $fieldSlug */
        $fieldSlug = $fieldValue[$fieldControlSlug] ?? null;

        if (array_key_exists($fieldId, $fields)) {
            /** @var DropdownData $field */
            $field = $fields[$fieldId];
            /** @var string $valueInField */
            $valueInField = $field['value'];

            $carry[$fieldSlug] = $valueInField;
        }

        return $carry;
    }, []);
}

/**
 * @param mixed|string $maybeJson
 * @return mixed|string
 */
function jsonDecodeIfValidJson($maybeJson) {
    if (!is_string($maybeJson)) {
        return $maybeJson;
    }

    try {
        return json_decode($maybeJson, true, 512, JSON_THROW_ON_ERROR);
    } catch (\JsonException $jsonException) {
        // Not valid JSON, let's just return the original value
        return $maybeJson;
    }
}

/**
 * @param StoredFormField $field
 * @return string
 */
function getIdFromField($field) {
    return $field['advanced']['id'] ?? sanitize_title($field['type'] . '_' . $field['label']);
}
