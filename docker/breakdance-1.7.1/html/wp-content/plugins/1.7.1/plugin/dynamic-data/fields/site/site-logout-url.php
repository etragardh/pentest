<?php

namespace Breakdance\DynamicData;

class SiteLogoutUrl extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Logout URL';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('redirect_url', 'Redirect URL', [
                'type' => 'text',
                'layout' => 'vertical',
            ])
        ];
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Site Info';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'site_logout_url';
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['string', 'url'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $redirectUrl = $attributes['redirect_url'] ?? '';
        $url = htmlspecialchars_decode(wp_logout_url($redirectUrl));

        return StringData::fromString($url);
    }
}
