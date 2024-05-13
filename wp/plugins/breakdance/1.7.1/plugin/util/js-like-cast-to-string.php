<?php
/**
 * @param mixed $value
 *
 * @return string
 */
function js_like_cast_to_string($value): string
{
    if ($value === false) {
        return 'false';
    } elseif ($value === null) {
        return 'null';
    } elseif ($value === true) {
        return 'true';
    } else {
        return "$value";
    }
}
