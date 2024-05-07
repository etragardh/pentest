<?php

namespace Breakdance\WooCommerce;

add_action( 'customize_register', '\Breakdance\WooCommerce\addProductsPerRowNotice' );

/**
 * Add settings to the customizer.
 * @param \WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function addProductsPerRowNotice($wp_customize)
{
    $wp_customize->add_setting(
        'woocommerce_catalog_columns',
        array(
            'default'              => getProductListSetting('products_per_row', 4),
            'type'                 => 'option',
            'capability'           => 'manage_woocommerce',
            'sanitize_callback'    => 'absint',
            'sanitize_js_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'woocommerce_catalog_columns',
        array(
            'label'       => __( 'Products per row', 'woocommerce' ),
            'description' => 'Customize Products per row in Breakdance at <strong>Global Settings » WooCommerce » Other » Products List</strong>',
            'section'     => 'woocommerce_product_catalog',
            'settings'    => 'woocommerce_catalog_columns',
            'type' => 'hidden',
            'input_attrs' => array(
                'disabled' => true,
                'min'  => wc_get_theme_support( 'product_grid::min_columns', 1 ),
                'max'  => wc_get_theme_support( 'product_grid::max_columns', '' ),
                'step' => 1,
            ),
        )
    );

}
