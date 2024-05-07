<?php

namespace Breakdance\DynamicData;

class WoocommerceProductRating extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Product Rating';
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
        return 'product_rating';
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
            \Breakdance\Elements\control('rating_type', 'Rating Type', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge([
                    ['text' => 'Rating', 'value' => 'rating'],
                    ['text' => 'Rating Count', 'value' => 'rating_count'],
                    ['text' => 'Review Count', 'value' => 'review_count'],
                ])
            ]),
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

        if (!$product || !array_key_exists('rating_type', $attributes)) {
            return StringData::emptyString();
        }

        if ($attributes['rating_type'] === 'rating') {
            return StringData::fromString($product->get_average_rating());
        }

        if ($attributes['rating_type'] === 'rating_count') {
            return StringData::fromString($product->get_rating_count());
        }

        if ($attributes['rating_type'] === 'review_count') {
            return StringData::fromString($product->get_review_count());
        }

        return StringData::emptyString();
    }
}
