<?php

namespace Breakdance\WooCommerce\Widgets;

// @psalm-ignore-file

function attribute_filter_get_attribute_array() {

    // this function needs to do basically the same thing that
    // WooCommerce's WC_Widget_Layered_Nav::get_instance_taxonomy does

    $atts = array_values(wc_get_attribute_taxonomies());

    return array_map(
        function($tax) {
            return [
                'text' => $tax->attribute_label,
                'value' => $tax->attribute_name
            ];
        },
        $atts
    );

}
