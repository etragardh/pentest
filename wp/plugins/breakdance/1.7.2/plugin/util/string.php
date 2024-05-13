<?php

namespace Breakdance\String;

/**
 * Convert the given string to lower-case.
 * @param string $value
 * @return string
 */
function lower($value)
{
    return mb_strtolower($value, 'UTF-8');
}

/**
 * Convert a string to snake case.
 * @param string $value
 * @param string $delimiter
 * @return string
 */
function snake($value, $delimiter = '_')
{
    /** @var array<string, array<string>> $snakeCache */
    static $snakeCache = [];
    $key = $value;

    if (isset($snakeCache[$key][$delimiter])) {
        return $snakeCache[$key][$delimiter];
    }

    if (! ctype_lower($value)) {
        $value = preg_replace('/\s+/u', '', ucwords($value));
        $value = lower(preg_replace('/(.)(?=[A-Z])/u', '$1'. $delimiter, $value));
    }

    return $snakeCache[$key][$delimiter] = $value;
}

/**
 * Convert a value to studly caps case.
 * @param string $value
 * @return string
 */
function studly($value)
{
    /** @var string[] $studlyCache */
    static $studlyCache = [];
    $key = $value;

    if (isset($studlyCache[$key])) {
        return $studlyCache[$key];
    }

    $value = ucwords(str_replace(['-', '_'], ' ', $value));
    return $studlyCache[$key] = str_replace(' ', '', $value);
}

/**
 * Convert a value to camel case.
 * @param string $value
 * @return string
 */
function camel($value)
{
    /** @var string[] $camelCache */
    static $camelCache = [];

    if (isset($camelCache[$value])) {
        return $camelCache[$value];
    }

    return $camelCache[$value] = lcfirst(studly($value));
}
