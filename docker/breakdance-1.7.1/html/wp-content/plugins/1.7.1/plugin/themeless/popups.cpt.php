<?php

namespace Breakdance\Themeless;

/**
 * @return void
 */
function register_popup_post_type()
{

    \register_post_type(
        BREAKDANCE_POPUP_POST_TYPE,
        array_merge(
            [
                'labels' => [
                    'name' => 'Popups',
                    'singular_name' => 'Popup',
                ],
            ],
            getTemplateCptsSharedArgs()
        )
    );

    \Breakdance\Util\disable_publishing_options_and_attributes_metabox_and_force_status_to_publish(BREAKDANCE_POPUP_POST_TYPE);
}
