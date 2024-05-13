<?php

namespace Breakdance\Data;

use function Breakdance\Config\Breakpoints\get_breakpoints;

class RecursivePropertyIterator extends \RecursiveArrayIterator
{
    public function hasChildren(): bool
    {
        /** @var mixed $property */
        $property = $this->current();
        if (!is_array($property)) {
            return false;
        }

        $propertyKeys = array_keys($property);

        // If this is an array with number, style, unit keys we don't need to descend
        if (!array_diff(['number', 'style', 'unit'], $propertyKeys)) {
            return false;
        }

        /** @var boolean $isDynamicMeta */
        $isDynamicMeta = str_ends_with((string) $this->key(), '_dynamic_meta');
        if ($isDynamicMeta){
            return false;
        }

        return true;
    }
}
