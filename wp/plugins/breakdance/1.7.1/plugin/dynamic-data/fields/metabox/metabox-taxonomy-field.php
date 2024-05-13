<?php

namespace Breakdance\DynamicData;

class MetaboxTaxonomyField extends MetaboxField
{

    public function returnTypes()
    {
        return ['string', 'url'];
    }

    public function handler($attributes): StringData
    {
        $terms = MetaboxField::getValue($this->field);
        $separator = $attributes['separator'] ?? ', ';
        if (!$terms) {
            return StringData::emptyString();
        }

        if (!is_array($terms)) {
            $terms = [$terms];
        }

        $output = array_map(static function($term) {
            return $term->name;
        }, $terms);
        return StringData::fromString(implode($separator, $output));
    }
}
