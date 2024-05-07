<?php

namespace Breakdance\DynamicData;

class StringData extends FieldData
{

    public string $value = '';

    /**
     * @param mixed $attributes
     * @return string
     */
    public function getValue($attributes = []): string
    {
        $value = $this->value;

        if (is_array($attributes)) {
            if (empty($value) && isset($attributes['fallback'])) {
                $value = (string) $attributes['fallback'];
            }

            if (array_key_exists('truncate', $attributes) && !empty($attributes['truncate'])) {
                $value = (string) $this->truncate($value, (int) $attributes['truncate']);
            }

            if (isset($attributes['beforecontent'])) {
                $value = (string) $attributes['beforecontent'] . (string) $value;
            }

            if (isset($attributes['aftercontent'])) {
                $value = (string) $value . (string) $attributes['aftercontent'];
            }
        }

        return $value;
    }

    public function hasValue()
    {
        return !empty($this->value);
    }

    public static function fromString(string $string): self
    {
        $stringData = new self;
        $stringData->value = $string;

        return $stringData;
    }

    public static function emptyString(): self
    {
        $stringData = new self;
        $stringData->value = '';

        return $stringData;
    }

    /**
     * @param string $value
     * @param int $limit
     * @return string
     */
    private function truncate($value, $limit)
    {
        if (strlen($value) <= $limit) {
            return $value;
        }
        $truncatedValue = substr($value, 0, $limit);
        return $truncatedValue . '...';
    }
}
