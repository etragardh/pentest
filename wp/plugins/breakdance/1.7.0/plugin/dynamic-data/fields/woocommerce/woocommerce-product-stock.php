<?php

namespace Breakdance\DynamicData;

class WoocommerceProductStock extends StringField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Product Stock';
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
        return 'product_stock';
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
            \Breakdance\Elements\control('in_stock_text', 'In Stock Text', [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            \Breakdance\Elements\control('out_of_stock_text', 'Out Of Stock Text', [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            \Breakdance\Elements\control('backorder_text', 'On Backorder Text', [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
        ];
    }

    public function defaultAttributes()
    {
        return [
            'in_stock_text' => 'In stock',
            'out_of_stock_text' => 'Out of stock',
            'backorder_text' => 'On backorder',
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

        $stockStatus = $product->get_stock_status();
        if (empty($stockStatus)) {
            return StringData::emptyString();
        }

        $messages = [
            'instock' => $attributes['in_stock_text'] ?? '',
            'outofstock' => $attributes['out_of_stock_text'] ?? '',
            'onbackorder' => $attributes['backorder_text'] ?? '',
        ];

        if (!array_key_exists($stockStatus, $messages)) {
            return StringData::emptyString();
        }

        return StringData::fromString($messages[$stockStatus]);
    }
}
