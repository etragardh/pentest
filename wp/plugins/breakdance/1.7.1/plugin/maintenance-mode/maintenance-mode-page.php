<?php

namespace Breakdance\MaintenanceMode;

use function Breakdance\Permissions\_getRoles;

$modeOptions = [
    'disabled' => 'Disabled',
    'coming-soon' => '(200) Coming Soon',
    'maintenance' => '(503) Maintenance',
];

$userStatuses = [
    'auth' => 'Logged in',
    'auth-with-status' => 'Logged in with role',
];

/** @var \WP_Post[] $pages */
$pages = get_pages();

$roles = _getRoles();
$userAuthStatus = (string) optionsGetter('status', 'auth')
?>

<form action="" method="post">
    <?php wp_nonce_field('breakdance_admin_maintenance-mode_tab'); ?>

    <h2>Maintenance</h2>

    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th class='valign-th-middle' scope="row">Mode</th>
                <td>
                    <select name="mode">
                    <?php foreach ($modeOptions as $key => $option) : ?>
                        <option
                            value="<?php echo $key; ?>"
                            <?php echo $key === optionsGetter('mode', 'disabled') ? 'selected' : ''; ?>
                        >
                            <?php echo $option; ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th class='valign-th-middle' scope="row">Page</th>
                <td>
                    <select name="page">
                        <option value="none">Select a page&hellip;</option>
                    <?php foreach ($pages as $page) : ?>
                        <option
                            value="<?php echo $page->ID; ?>"
                            <?php echo (string) $page->ID === optionsGetter('page', 'none') ? 'selected' : ''; ?>
                        >
                            <?php echo $page->post_title; ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                    <p class="description">
                        <small>
                            <em>This will be displayed to the user when maintenance mode is enabled.</em>
                        </small>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <h2>Access Control</h2>
    <p>Users can bypass maintenance mode using any of the options listed below.</p>

    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th class='valign-th-middle' scope="row">Authentication Status</th>
                <td>
                    <fieldset>
                        <select name="status" id="breakdance-maintenance-roles-selector-input">
                        <?php foreach ($userStatuses as $key => $value) : ?>
                            <option
                                value="<?php echo $key; ?>"
                                <?php echo $key === optionsGetter('status', 'auth') ? 'selected' : ''; ?>
                            >
                                <?php echo $value; ?>
                            </option>
                        <?php endforeach; ?>
                        </select>
                    </fieldset>

                    <div id="breakdance-maintenance-user-roles"
                         style="<?php echo $userAuthStatus === 'auth-with-status' ? '' : 'display: none;' ?>"
                    >
                        <h4>User Roles</h4>
                        <fieldset>
                        <?php foreach ($roles as $key => $role) : ?>
                            <label for="breakdance-maintenance-user-role-<?php echo $key; ?>">
                                <input
                                    type="checkbox"
                                    name="user-roles[]"
                                    value="<?php echo $key; ?>"
                                    id="breakdance-maintenance-user-role-<?php echo $key; ?>"
                                    <?php echo in_array($key, (array) optionsGetter('user-roles')) ? 'checked' : ''; ?>
                                />
                                <span>
                                <?php
                                    echo $role;
                                ?>
                                </span>
                            </label>
                            <br />
                        <?php endforeach; ?>
                        </fieldset>
                    </div>
                </td>
            </tr>
            <tr>
                <th class='valign-th-middle' scope="row">URL param</th>
                <td>
                    <fieldset>
                        <label for="breakdance-maintenance-enable-url-params">
                            <input
                                type="checkbox"
                                name="url-params"
                                id="breakdance-maintenance-enable-url-params"
                                <?php echo optionsGetter('url-params') === "on" ? "checked" : ""; ?>
                            />
                            <span>Enable URL Param</span>
                        </label>
                        <br />
                        <code><?php echo get_home_url(); ?>?</code>
                        <input
                            name="url-params-key"
                            type="text"
                            placeholder="key"
                            value="<?php echo (string) optionsGetter('url-params-key'); ?>"
                        >
                        <code> =</code>
                        <input
                            name="url-params-value"
                            type="text"
                            placeholder="value"
                            value="<?php echo (string) optionsGetter('url-params-value'); ?>"
                        />
                        <?php if (optionsGetter('url-params') === 'on') : ?>
                            <button
                                type="button"
                                class="button"
                                id="breakdance-maintenance-url-params-copy-button"
                                data-clipboard-text="<?php echo getGeneratedURLParamsURL(); ?>"
                            >
                                Copy
                            </button>
                        <?php endif; ?>
                    </fieldset>
                    <p class="description">
                        <small>
                            <em>Users with any of the URL parameters defined above set will have access to the site.</em>
                        </small>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <p class="submit">
        <input
            type="submit"
            name="breakdance-maintenance-mode-submit"
            id="submit"
            class="button button-primary"
            value="Save Changes"
        />
    </p>
</form>


<script>
    (function ($) {
        const $rolesBox = $('#breakdance-maintenance-user-roles');
        const $rolesSelectorInput = $('#breakdance-maintenance-roles-selector-input');

        $rolesSelectorInput.on('change', function () {
            $value = $(this).val();

            if ($value === 'auth-with-status') {
                $rolesBox.show();
            } else {
                $rolesBox.hide();
            }
        });
    }(jQuery));
</script>

<?php if (optionsGetter('url-params') === 'on') : ?>
<script>
    (function ($) {
        const $copyButton = $('#breakdance-maintenance-url-params-copy-button');

        $copyButton.click(function () {
            const self = $(this);
            const originalText = self.text();

            self.text('Copied');
            setTimeout(function () {
                self.text(originalText);
            }, 1500)
        });

        new ClipboardJS('.button');
    }(jQuery));
</script>
<?php endif; ?>
