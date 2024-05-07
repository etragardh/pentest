<?php

namespace Breakdance\DynamicData;

class WoocommerceProductImageURL extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Product Image (URL)';
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
        return 'product_image_url';
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
        ];
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['url'];
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

        $imageId = $product->get_image_id();
        $imageUrl = wp_get_attachment_image_url($imageId, 'full') ?? '';
        return StringData::fromString($imageUrl);
    }
}
