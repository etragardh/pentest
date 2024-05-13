<?php

/**
 * @param array $arr
 * @param string $path
 * @param mixed $value
 * @side-effect modifies $arr
 */
function assignArrayByPath(&$arr, $path, $value)
{

    $exploded = explode('.', $path);

    /**
     * @psalm-suppress MixedArrayAccess
     */
    $temp = &$arr;
    foreach ($exploded as $key) {
        if (is_numeric($key) && (int)$key == $key) {
            // supports accessing repeaters like some.repeater.4.item
            $key = (int)$key;
        }

        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-suppress MixedAssignment
         */
        $temp = &$temp[$key];
    }
    /**
     * @psalm-suppress MixedAssignment
     */
    $temp = $value;
    unset($temp);
}

/**
 * like lodash get but php
 * @param array $arr
 * @param string $path
 * @param mixed $defaultValue
 *
 * @return mixed
 */
function readFromArrayByPath($arr, $path, $defaultValue = '')
{
    /**
     * @var false|string[]
     */
    $keys = explode('.', $path);

    if (!$keys) {
        return $defaultValue;
    }

    foreach ($keys as $key) {
        if (is_array($arr) && array_key_exists($key, $arr)) {
            /**
             * @psalm-suppress MixedArrayAccess
             * @psalm-suppress MixedAssignment
             */
            $arr = $arr[$key];
        } else {
            return $defaultValue;
        }
    }

    /**
     * @psalm-suppress MixedReturnStatement
     */
    return $arr;
}

/**
 * @param PropertiesData $properties
 * @param string $partialPath
 * @param string $fullPath
 * @return false|mixed|array{dynamicPropertyPath: string, value: string}[]
 */
function readPropertiesValueFromDynamicPropertyPath($properties, $partialPath, $fullPath)
{

    /**
     * @var false|string[]
     */
    $keys = explode('.', $partialPath);

    if (!$keys) {
        return false;
    }

    /**
     * @var PropertiesData
     */
    $propertyValueAtKey = $properties;
    foreach ($keys as $key) {
        if (!$propertyValueAtKey) {
            return false;
        }

        /*
        * Recursively loop through the path each time there's a "[]", which denotes a repeater
        * Until it reaches the end of the path and gets the value
        * Then it'll return an array of all values from the path in that repeater
        * Or "false" if the array doesn't exist
        */
        if (strpos($key, '[]') !== false) {
            $keyWithoutBrackets = str_replace('[]', '', $key);

            if (!isset($propertyValueAtKey[$keyWithoutBrackets]) ||
                // this is just for safety
                !is_array($propertyValueAtKey[$keyWithoutBrackets])
            ) {
                return false;
            }

            $matches = [];
            // Get everything after the first "[]."
            preg_match('/(\[].)(.*)/', $partialPath, $matches);
            // Matches: 0 = match 1. 1 =  first group. 2 = second group
            $pathAfterCurrentKey = $matches[2] ?? '';

            /**
             * @var array{dynamicPropertyPath: string, value: string}[]
             */
            $arrayOfValues = [];

            /**
             * @psalm-suppress MixedArrayAccess
             * @psalm-suppress MixedAssignment
             */
            foreach ($propertyValueAtKey[$keyWithoutBrackets] as $index => $props) {
                // "content.very_nested.repeater[].repeater2[].link.url"  becomes "content.very_nested.repeater1.4.repeater2[].link.url"
                $fullPathWithIndexInsteadOfBrackets = replaceFirstOccurrence($fullPath, "[]", ".{$index}");
                $valueOrArrayOfValuesFromPath = readPropertiesValueFromDynamicPropertyPath(
                    $props,
                    $pathAfterCurrentKey,
                    $fullPathWithIndexInsteadOfBrackets
                );

                /*
                 * "reduce" all values onto a single array.
                 * Even a deeply nested path will result in a simple array
                 */
                if (is_array($valueOrArrayOfValuesFromPath)) {
                    foreach ($valueOrArrayOfValuesFromPath as $dynamicPathAndValue) {
                        /*
                         * if the "dynamicPropertyPath" is wrong, and points to an object instead of the correct value
                         * $dynamicPathAndValue will be a property object instead of the correct data
                         * So we check for that edge case
                         */
                        if (isset(
                            $dynamicPathAndValue['value'],
                            $dynamicPathAndValue['dynamicPropertyPath']
                        )) {
                            array_push(
                                $arrayOfValues,
                                $dynamicPathAndValue
                            );
                        }
                    }
                } else if ($valueOrArrayOfValuesFromPath) {
                    array_push(
                        $arrayOfValues,
                        [
                            'dynamicPropertyPath' => $fullPathWithIndexInsteadOfBrackets,
                            'value' => $valueOrArrayOfValuesFromPath
                        ]
                    );
                }
            }

            return $arrayOfValues;
        } else {
            /**
             * @psalm-suppress MixedArrayAccess
             * @psalm-suppress MixedAssignment
             */
            $propertyValueAtKey = $propertyValueAtKey[$key] ?? false;
        }
    }

    /**
     * @psalm-suppress MixedReturnStatement
     */
    return $propertyValueAtKey ?: false;
}

// https://stackoverflow.com/a/1252710/5993042
/**
 * @param string $haystack
 * @param string $needle
 * @param string $replaceWith
 * @return string
 */
function replaceFirstOccurrence($haystack, $needle, $replaceWith)
{
    $position = strpos($haystack, $needle);

    if ($position !== false) {
        return substr_replace($haystack, $replaceWith, $position, strlen($needle));
    }

    return $haystack;
}

if (!function_exists("array_is_list")) {
    /**
     * A polyfill for the array_is_list function from PHP 8.1
     *
     * @param array $array
     * @return bool
     */
    function array_is_list($array) {
        $i = -1;
        /** @psalm-suppress MixedAssignment */
        foreach ($array as $k => $v) {
            ++$i;
            if ($k !== $i) {
                return false;
            }
        }
        return true;
    }
}
