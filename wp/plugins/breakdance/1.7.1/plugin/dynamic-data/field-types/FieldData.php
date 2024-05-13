<?php

namespace Breakdance\DynamicData;

abstract class FieldData {
    /**
     * @param mixed $attributes
     * @return mixed
     */
    abstract public function getValue($attributes = []);

    /**
     * @return bool
     */
    abstract public function hasValue();
}
