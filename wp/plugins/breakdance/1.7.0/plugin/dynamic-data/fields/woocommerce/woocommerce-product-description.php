<?php

namespace Breakdance\DynamicData;

class WoocommerceProductDescription extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Product Description';
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
        return 'product_description';
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
            \Breakdance\Elements\control('description_type', 'Description Type', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge([
                    ['text' => 'Short Description', 'value' => 'short'],
                    ['text' => 'Long Description', 'value' => 'long'],
                ]),
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultAttributes()
    {
        return [
            'description_type' => 'long'
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
        if (!$product || !array_key_exists('description_type', $attributes)) {
            return StringData::emptyString();
        }

        if ($attributes['description_type'] === 'short') {
            return StringData::fromString($product->get_short_description());
        }
        return StringData::fromString($product->get_description());
    }
}
