<?php

namespace Breakdance\Data;

/**
 * @param int $post_id
 * @param string $field_name
 * @param mixed $field_value - should be serializable
 * @return void
 */
function set_meta($post_id, $field_name, $field_value)
{
    update_post_meta($post_id, $field_name, encode_before_writing_to_wp($field_value, true));
}

/**
 * @param int $post_id
 * @param string $field_name
 * @param false|string $array_key - if the value stored in $field_name is an array, rather than return the whole array, specifying $key_name will return the value of a certain key
 * @return mixed|false
 */
function get_meta($post_id, $field_name, $array_key = false)
{
    /** @var mixed */
    $value = \get_post_meta($post_id, $field_name, true);

    /** @var mixed|false $decoded_value */
    $decoded_value = decode_after_reading_from_wp($value);

    if ($array_key) {
        return pick_from_decoded_value($decoded_value, $array_key);
    }

    return $decoded_value;

}
