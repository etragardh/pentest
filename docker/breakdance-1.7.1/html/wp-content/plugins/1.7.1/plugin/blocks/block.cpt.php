<?php

namespace Breakdance\Blocks;

use function Breakdance\Themeless\getTemplateCptsSharedArgs;

/**
 * @return void
 */
function register_post_type()
{
    \register_post_type(
        BREAKDANCE_BLOCK_POST_TYPE,
        array_merge(
            [
                'labels' => [
                    'name' => 'Global Blocks',
                    'singular_name' => 'Global Block',
                ],
            ],
            getTemplateCptsSharedArgs()
        )
    );

    \Breakdance\Util\disable_publishing_options_and_attributes_metabox_and_force_status_to_publish(BREAKDANCE_BLOCK_POST_TYPE);

}
