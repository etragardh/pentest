<?php

namespace Breakdance\Admin\SettingsPage\PostTypesTab;

use function Breakdance\Util\get_public_and_allowed_post_types;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\Admin\SettingsPage\PostTypesTab\register');

function register()
{
    \Breakdance\Admin\SettingsPage\addTab('Post Types', 'post_types', '\Breakdance\Admin\SettingsPage\PostTypesTab\tab', 1199);
}

function tab()
{
    $nonce_action = 'breakdance_admin_post-types_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        if (array_key_exists('all', $_POST)) {
            $selectedPostTypes = get_public_and_allowed_post_types();
        } else {
            /**
             * @psalm-suppress MixedAssignment
             */
            $selectedPostTypes = filter_input(INPUT_POST, 'post_types', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
        }

        \Breakdance\Data\set_global_option('breakdance_settings_enabled_post_types', $selectedPostTypes ?? []);
    }

    // load data for use in form

    // TODO: type casting this shit is too dangerous
    /**
     * @var string[]|mixed
     */
    $selectedPostTypes = \Breakdance\Data\get_global_option('breakdance_settings_enabled_post_types');
    if (!is_array($selectedPostTypes)) {
        $selectedPostTypes = [];
    }

    ?>

    <h2>Post Types</h2>

    <form id="post_type_form" action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        Post Types
                    </th>
                    <td>
                        <fieldset>
                            <label for="post_types_all">
                                <input type="checkbox" name="all" value="all" id="post_types_all"> All Public & Allowed Post Types
                            </label><br />
                            <?php

    echo implode(
        array_map(
            function ($postType) use ($selectedPostTypes) {

                $checked = in_array($postType, $selectedPostTypes) ? "checked" : "";
                $postTypeObject = get_post_type_object($postType);
                /** @var string $label */
                $label = $postTypeObject ? $postTypeObject->labels->singular_name : $postType;

                return <<<HTML
                                <label for="post_types_{$postType}">
                                    <input type="checkbox" name="post_types[]" value="{$postType}" {$checked} id="post_types_{$postType}"> {$label}
                                </label><br />
                                HTML;
            },
            get_public_and_allowed_post_types()
        )
    );

    ?>

                        </fieldset>

                        <p class="description">Editing with Breakdance will only be enabled for the above post types.</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>

    </form>

    <script>
        jQuery(function ($) {
            const postTypeForm = $('#post_type_form');
            const allTypesInput = $('#post_types_all');
            const postTypeInputs = $('input[name="post_types[]"]');

            allTypesInput.on('change', function (event) {
                // stop the propagation as we don't want this
                // to bubble up and trigger the postTypeForm change handler
                event.stopPropagation();

                if (event.target.checked) {
                    postTypeInputs.prop('checked', true).prop('disabled', true);
                } else {
                    postTypeInputs.prop('disabled', false);
                }
            });

            postTypeForm.on('change', function () {
                const selectedInputs = $('input[name="post_types[]"]:checked');

                if (selectedInputs.length === postTypeInputs.length) {
                    allTypesInput.prop('checked', true).trigger('change');
                }
            });

            // trigger change on pageload to correctly
            // set the initial state of the 'all' input
            postTypeForm.trigger('change');
        });
    </script>
    <?php
}
