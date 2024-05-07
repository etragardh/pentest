<?php

namespace Breakdance\Themeless;

/**
 * @return void
 */
function register_header_post_type()
{
    \register_post_type(
        BREAKDANCE_HEADER_POST_TYPE,
        array_merge(
            [
                'labels' => [
                    'name' => 'Headers',
                    'singular_name' => 'Header',
                ],
            ],
            getTemplateCptsSharedArgs()
        )
    );

    \Breakdance\Util\disable_publishing_options_and_attributes_metabox_and_force_status_to_publish(BREAKDANCE_HEADER_POST_TYPE);
}


/**
 * @return void
 */
function register_footer_post_type()
{

    \register_post_type(
        BREAKDANCE_FOOTER_POST_TYPE,
        array_merge(
            [
                'labels' => [
                    'name' => 'Footers',
                    'singular_name' => 'Footer',
                ],
            ],
            getTemplateCptsSharedArgs()
        )
    );

    \Breakdance\Util\disable_publishing_options_and_attributes_metabox_and_force_status_to_publish(BREAKDANCE_FOOTER_POST_TYPE);
}
