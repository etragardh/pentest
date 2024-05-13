<?php

namespace EssentialElements;

/**
 * @param int $id
 * @return false|string
 */
function renderContactForm7($id)
{
    if ($id) {
        return do_shortcode("[contact-form-7 id='{$id}']");
    }

    return \Breakdance\Forms\getEmptyTemplate();
}

/**
 * @return array
 */
function getContactForm7Options()
{
    /** @var \WP_Post[] $forms */
    $forms = get_posts([
        'post_type' => 'wpcf7_contact_form',
        'posts_per_page' => -1
    ]);

    return array_map(
    /**
     * @param \WP_Post $form
     * @return array
     */
        function ($form) {
            return [
                'text' => $form->post_title,
                'value' => $form->ID
            ];
        },
        $forms);
}
