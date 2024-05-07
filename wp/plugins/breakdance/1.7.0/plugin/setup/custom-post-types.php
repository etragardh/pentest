<?php

namespace Breakdance\Setup;

use function Breakdance\Forms\Submission\registerPostType as registerFormSubmissionPostType;
use function Breakdance\Blocks\register_post_type as registerBlockPostType;
use function Breakdance\Blocks\register_acf_post_type as registerAcfBlockPostType;
use function Breakdance\Themeless\register_footer_post_type;
use function Breakdance\Themeless\register_header_post_type;
use function Breakdance\Themeless\register_popup_post_type;
use function Breakdance\Themeless\register_template_post_type;


add_action('init', 'Breakdance\Setup\register_custom_post_types');

function register_custom_post_types()
{
    registerFormSubmissionPostType();
    registerBlockPostType();
    registerAcfBlockPostType();
    register_header_post_type();
    register_footer_post_type();
    register_popup_post_type();
    register_template_post_type();
}
