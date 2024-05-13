<?php

namespace Breakdance\Util;

// https://stackoverflow.com/a/173479
// Note that it returns TRUE in the (ambiguous) case of an empty array.

/**
 * @param array $arr
 * @return bool
 */
function isAssoc(array $arr)
{
    if (array() === $arr) {
        return false;
    }

    /**
     * @psalm-suppress DocblockTypeContradiction
     */
    return array_keys($arr) !== range(0, count($arr) - 1);
}
