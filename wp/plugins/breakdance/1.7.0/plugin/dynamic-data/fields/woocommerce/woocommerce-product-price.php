<?php

namespace Breakdance\DynamicData;

class WoocommerceProductPrice extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Product Price';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'WooCommerce';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'product_price';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('product', 'Product', [
                    'type' => 'post_chooser',
                    'layout' => 'vertical',
                    'postChooserOptions' => [
                        'multiple' => false,
                        'showThumbnails' => false,
                        'postType' => 'Product'
                    ]
                ]
            ),
            \Breakdance\Elements\control('price_type', 'Price Type', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge([
                    ['text' => 'Regular Price', 'value' => 'regular'],
                    ['text' => 'Sale Price', 'value' => 'sale'],
                ])
            ]),
        ];
    }

    public function defaultAttributes()
    {
        return [
            'price_type' => 'regular'
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        global $post;
        $productId = $post->ID ?? null;
        if (!empty($attributes['product'])) {
            $productId = $attributes['product'];
        }
        $product = wc_get_product($productId);

        if (!$product) {
            return StringData::emptyString();
        }

        $priceType = $attributes['price_type'];

        if ($product->is_type('variable')) {
            $variationPriceRange = '';
            if ($priceType === 'regular') {
                $variationPriceRange = $product->get_variation_regular_price('min', true) . ' - ' . $product->get_variation_regular_price('max', true);
            }

            if ($priceType === 'sale') {
                $variationPriceRange = $product->get_variation_sale_price('min', true) . ' - ' . $product->get_variation_sale_price('max', true);
            }

            return StringData::fromString($variationPriceRange);
        }

        if ($priceType === 'regular') {
            return StringData::fromString($product->get_regular_price());
        }

        if ($priceType === 'sale') {
            return StringData::fromString($product->get_sale_price());
        }
        return StringData::emptyString();
    }
}
