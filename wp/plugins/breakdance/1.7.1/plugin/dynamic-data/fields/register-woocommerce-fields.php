<?php

namespace Breakdance\DynamicData;

/** Woocommerce */
if (class_exists('woocommerce')) {
    $woocommerce_fields = [
        new WoocommerceProductDescription(),
        new WoocommerceProductImageURL(),
        new WoocommerceProductImage(),
        new WoocommerceProductPrice(),
        new WoocommerceProductRating(),
        new WoocommerceProductSale(),
        new WoocommerceProductSKU(),
        new WoocommerceProductStock(),
        new WoocommerceProductTerms(),
        new WoocommerceProductTitle(),
        new WoocommerceProductGallery(),
        new WoocommerceProductGalleryImage(),
    ];

    foreach ($woocommerce_fields as $field) {
        DynamicDataController::getInstance()->registerField($field);
    }
}
