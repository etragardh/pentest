<?php

namespace Breakdance\Render;


/**
 * public version that just returns an array by default
 * @param string $breakpointId
 * @param PropertiesData $properties
 * @param string[] $allBreakpointIds
 * @param string $baseBreakpointId
 * @param string[]|false $whitelistedPropertyPaths
 * @return PropertiesData // can return null
 */
function getFlattenedPropertiesByBreakpoint(
    $breakpointId,
    $properties,
    $allBreakpointIds,
    $baseBreakpointId,
    $whitelistedPropertyPaths
)
{
    return _getFlattenedPropertiesByBreakpoint(
        $breakpointId,
        $properties,
        $allBreakpointIds,
        $baseBreakpointId,
        $whitelistedPropertyPaths ?: [],
        ""
    )
        // It can return null so we return [] by default
        ?: [];
}


/**
 * This function is also in TS and they must both mirror each other's logic
 *
 * For better comments check the TS version.
 * @param string $breakpointId
 * @param PropertiesData $properties
 * @param string[] $allBreakpointIds
 * @param string $baseBreakpointId
 * @param string[] $whitelistedPropertyPaths
 * @param string $parentPath
 * @return PropertiesData // can return null
 */
function _getFlattenedPropertiesByBreakpoint(
    $breakpointId,
    $properties,
    $allBreakpointIds,
    $baseBreakpointId,
    $whitelistedPropertyPaths,
    $parentPath
)
{
    // A difference with the TS version is that in PHP, properties are always arrays
    if (is_array($properties)) {
        $accumulator = [];

        /**
         * @psalm-suppress MixedAssignment
         */
        foreach ($properties as $key => $property) {
            if ($key === $breakpointId) {
                return $property;
            }


            if (in_array($key, $allBreakpointIds, true)) {
                continue;
            }

            /**
             * A sequential array (as in a JS array) will only have integer keys
             * Whereas non-int will be from an associate array (as in a JS object)
             * This is an assumption because an associate array could have an int key
             * But given how we create properties, this shouldn't happen
             *
             * We could check if the entire array is sequential, but this is much more performant
             * https://stackoverflow.com/a/173479/5993042
             */
            $isSequentialArray = is_int($key);
            $currentPath = $parentPath ?
                $parentPath . ($isSequentialArray ? "[]" : ".{$key}")
                : "{$key}";

            /**
             * @var PropertiesData
             */
            $result = _getFlattenedPropertiesByBreakpoint(
                $breakpointId,
                $property,
                $allBreakpointIds,
                $baseBreakpointId,
                $whitelistedPropertyPaths,
                $currentPath

            );

            // We want to add null to sequential arrays because the order matters
            if ($result !== null || $isSequentialArray) {
                $accumulator[$key] = $result;
            }
        }

        $accumulatorWithoutNullValues = array_filter($accumulator, function ($value) {
            return $value !== null;
        });

        if (count($accumulatorWithoutNullValues) === 0) {
            return null;
        }

        // a repeater can be empty so we assign null instead of [];
        return count($accumulator) ? $accumulator : null;
    }

    $isCurrentPathWhitelisted =
        $parentPath &&
        array_filter($whitelistedPropertyPaths, function($propertyPath) use ($parentPath) {
            /** @var bool */
            return str_starts_with($parentPath, $propertyPath);
        });

    return $breakpointId === $baseBreakpointId || $isCurrentPathWhitelisted
        ? $properties
        : null;
}

