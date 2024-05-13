<?php

namespace Breakdance\DynamicData;

use function Breakdance\Util\WP\safe_get_terms;

class AcfTaxonomyField extends AcfField
{

    public function controls()
    {
        return [
            \Breakdance\Elements\control('separator', 'Separator', [
                'type' => 'text',
            ]),
        ];
    }

    public function returnTypes()
    {
        return ['string', 'url'];
    }

    public function handler($attributes): StringData
    {
        $isSubfield = array_key_exists('parent_type', $this->field);
        if ($isSubfield) {
            $settings = get_sub_field_object($this->field['name']);
        } else {
            $settings = get_field_object($this->field['name']);
        }

        if (empty($settings)) {
            return StringData::emptyString();
        }

        $ids = AcfField::getValue($this->field, false);
        if (empty($ids)) {
            return StringData::emptyString();
        }
        $terms = safe_get_terms($settings['taxonomy'], [
            'include' => $ids,
            'hide_empty' => false,
        ]);

        $output = wp_list_pluck($terms, 'name');

        $separator = $attributes['separator'] ?? ', ';
        return StringData::fromString(implode($separator, $output));
    }
}
