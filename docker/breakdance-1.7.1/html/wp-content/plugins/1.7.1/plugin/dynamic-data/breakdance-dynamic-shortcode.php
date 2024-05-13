<?php

namespace Breakdance\DynamicData;

/**
 * @param mixed $atts
 * @return string | array
 * @throws \Exception
 */
function breakdanceShortcodeHandler($atts)
{
    if (!is_array($atts) || !array_key_exists('field', $atts)) {
        return "";
    }

    $field = DynamicDataController::getInstance()->getField((string) $atts['field']);

    if ($field) {
        ob_start();

        $fieldData = $field->handler($atts);

        /** @var string | array $output */
        $output = $fieldData->getValue($atts);

        $unexpectedOutput = ob_get_clean();

        if ($unexpectedOutput) {
            throw new \Exception('Unexpected Dynamic Data Output:' . $unexpectedOutput);
        }

        if (isset($atts['process_value']) && ($atts['process_value'] ?? false)) {
            $output = pipeValueThroughProcessValueReturn($output, (string) $atts['process_value']);
        }

        return $output;
    }

    return "";
}

/**
 * Parse a [breakdance_dynamic] shortcode and return its value
 * @param string $shortcode
 * @return string | array
 * @throws \Exception
 */
function breakdanceDoShortcode($shortcode = '')
{
    $atts = getAttributesFromShortcode($shortcode);
    return breakdanceShortcodeHandler($atts);
}

/**
 * @param string $shortcode
 * @return array|string
 */
function getAttributesFromShortcode($shortcode) {
    // Shortcodes should end with ' /]', otherwise shortcode_parse_atts doesn't work.
    $shortcode = preg_replace('/\/?]/', ' /]', $shortcode);
    return shortcode_parse_atts($shortcode);
}

/**
 * @param string $shortcode
 * @return FieldData|null
 * @throws \Exception
 */
function getFieldDataFromShortcode($shortcode) {
    $attributes = getAttributesFromShortcode($shortcode);
    if (!is_array($attributes) || !array_key_exists('field', $attributes)) {
        return null;
    }

    $field = DynamicDataController::getInstance()->getField((string) $attributes['field']);
    if ($field) {
        ob_start();

        $fieldData = $field->handler($attributes);

        $unexpectedOutput = ob_get_clean();

        if ($unexpectedOutput) {
            throw new \Exception('Unexpected Dynamic Data Output:' . $unexpectedOutput);
        }

        return $fieldData;
    }

    return null;
}
