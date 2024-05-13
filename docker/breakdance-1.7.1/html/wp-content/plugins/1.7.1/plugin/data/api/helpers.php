<?php

namespace Breakdance\Data;

/**
 * @param mixed $value_to_encode
 * @param boolean $add_slashes
 * @return string|false
 */
function encode_before_writing_to_wp($value_to_encode, $add_slashes)
{
    $json_encoded = wp_json_encode($value_to_encode);

    if ($json_encoded === false) {
        return false;
    } else {
        if ($add_slashes) {
            /** @var string $slashed */
            $slashed = wp_slash($json_encoded);

            return $slashed;
        } else {
            return $json_encoded;
        }
    }
}

/**
 * @param mixed $value_to_decode
 * @return mixed|false
 */
function decode_after_reading_from_wp($value_to_decode)
{
    // Either JSON- or base64-encoded string is expected
    if (!is_string($value_to_decode)) {
        return false;
    }

    // TODO: json_decode is dangerous to be used like this, replace its usages over the project with some wrapper
    /** @var mixed */
    $json_decoded = json_decode($value_to_decode, true);

    if ($json_decoded !== null) {
        return $json_decoded;
    } else {
        return false;
    }
}

/**
 * @param mixed $decoded_value
 * @param string $array_key
 * @return mixed
 */
function pick_from_decoded_value($decoded_value, $array_key)
{
    if (is_array($decoded_value) && array_key_exists($array_key, $decoded_value)) {
        return $decoded_value[$array_key];
    } else {
        // TODO - throw? warn? return null?
        return $decoded_value;
    }
}
