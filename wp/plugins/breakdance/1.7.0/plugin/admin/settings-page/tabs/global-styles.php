<?php

namespace Breakdance\Admin\SettingsPage\GlobalStylesTab;

use function Breakdance\Admin\get_browse_mode_url_with_return_back_to_current_page;

add_action(
    'breakdance_register_admin_settings_page_register_tabs',
    '\Breakdance\Admin\SettingsPage\GlobalStylesTab\register'
);

function register()
{
    \Breakdance\Admin\SettingsPage\addTab(
        'Global Styles',
        'global_styles',
        '\Breakdance\Admin\SettingsPage\GlobalStylesTab\tab',
        100
    );
}

function tab()
{
?>
    <h2>Global Styles</h2>

<table class="form-table" role="presentation">
    <tbody>
    <tr>
        <th scope="row">
            Edit Global Styles
        </th>
        <td class='breakdance-launcher-row'>
            <a class="breakdance-browse-mode-button" href="<?= get_browse_mode_url_with_return_back_to_current_page(); ?>">Launch Breakdance</a>
            <p class="description" style='margin-top: 25px;'>Edit Global Settings & Selectors while browsing through your website.</p>
        </td>
    </tr>
    </tbody>
</table>

<style>
.breakdance-launcher-row {
    padding: 40px !important;
}

.breakdance-browse-mode-button {
    --breakdance-launcher-primary-color: #ffc514;
    --breakdance-launcher-secondary-color: #f6b800;
    color: black !important;
    line-height: 1;
    border: none;
    cursor: pointer;
    background-color: var(--breakdance-launcher-primary-color);
    transition: 0.3s background-color ease;
    text-decoration: none !important;
    font-size: 13px;
    font-weight: 500;
    border-radius: 0px;
    padding: 10px 20px;
    margin-bottom: 16px;
}
</style>

<?php
}


?>
