<?php

namespace Breakdance\Admin;

/**
 * @param int $post_id
 * @param boolean $convert_content
 * @return void
 */
function turn_off_breakdance($post_id, $convert_content = false)
{

    $html = (string) \Breakdance\Render\render($post_id);

    $cleaned_content = "";

    if ($convert_content === true) {

        $cleaned_content = clean_breakdance_html_to_wordpress_html($html);

        wp_update_post([
            'ID' => $post_id,
            'post_content' => $cleaned_content,
        ]);

    }

    delete_post_meta($post_id, 'breakdance_data');

}

/**
 * @param string $html
 * @return string
 */
function clean_breakdance_html_to_wordpress_html($html)
{
    return \Breakdance\Util\clean_breakdance_html_to_wordpress_html($html);
}

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler('breakdance_disable_and_maybe_extract', 'Breakdance\Admin\ajax_disable_and_maybe_extract', 'edit');
});

/**
 * @return array
 */
function ajax_disable_and_maybe_extract()
{
    $id = (int) filter_input(INPUT_POST, 'id');
    $should_extract = (bool) filter_input(INPUT_POST, 'should_extract');

    if ($should_extract) {
        turn_off_breakdance($id, true);
    } else {
        turn_off_breakdance($id, false);
    }

    return [];
}
