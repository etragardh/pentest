<?php

namespace Breakdance\Themeless;

/**
 * @return void
 */
function register_template_post_type()
{

    \register_post_type(
        BREAKDANCE_TEMPLATE_POST_TYPE,
        array_merge(
            [
                'labels' => [
                    'name' => 'Templates',
                    'singular_name' => 'Template',
                ],
            ],
            getTemplateCptsSharedArgs()
        )
    );

    \Breakdance\Util\disable_publishing_options_and_attributes_metabox_and_force_status_to_publish(BREAKDANCE_TEMPLATE_POST_TYPE);
}
