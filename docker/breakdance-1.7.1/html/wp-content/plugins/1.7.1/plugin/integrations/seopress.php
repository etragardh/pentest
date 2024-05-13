<?php

namespace Breakdance\Admin\Seo;

use function Breakdance\Admin\get_mode;
use function Breakdance\Data\get_tree_as_html;

add_filter('seopress_content_analysis_content', 'Breakdance\Admin\Seo\custom_analysis', 10, 2);

/**
 * @param string $content
 * @param integer $id
 * @return string
 */
function custom_analysis($content, $id)
{
    return get_mode($id) === 'breakdance' ? get_tree_as_html((int) $id) : $content;
}
