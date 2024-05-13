<?php

namespace Breakdance\Setup;

use function Breakdance\Filesystem\check_all_required_directories;
use function Breakdance\Filesystem\HelperFunctions\generate_error_msg_from_fs_wp_error;
use function Breakdance\Filesystem\try_to_create_all_required_directories;
use function Breakdance\Subscription\isFreeMode;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', function () {
    \Breakdance\Admin\SettingsPage\addTab('Tools', "tools", 'Breakdance\Setup\settings_page', 2000);
});

const BREAKDANCE_SETUP_NONCE_ACTION_TOTAL_RESET = 'BREAKDANCE_SETUP_NONCE_ACTION_TOTAL_RESET';
const BREAKDANCE_SETUP_NONCE_ACTION_SOFT_RESET = 'BREAKDANCE_SETUP_NONCE_ACTION_SOFT_RESET';
const BREAKDANCE_SETUP_NONCE_ACTION_CREATE_DIRECTORIES = 'BREAKDANCE_SETUP_NONCE_ACTION_CREATE_DIRECTORIES';
const BREAKDANCE_SETUP_NONCE_ACTION_DOWNLOAD_EXPORT_FILE = 'BREAKDANCE_SETUP_NONCE_ACTION_UPLOAD_EXPORT_FILE';
const BREAKDANCE_SETUP_NONCE_ACTION_UPLOAD_EXPORT_FILE = 'BREAKDANCE_SETUP_NONCE_ACTION_UPLOAD_EXPORT_FILE';
const BREAKDANCE_SETUP_NONCE_ACTION_REPLACE_URL = 'BREAKDANCE_SETUP_NONCE_ACTION_REPLACE_URL';

function admin_notice(string $message, string $type = 'success'): void
{
    ?>
    <div class="notice notice-<?php _e($type);?> is-dismissible">
        <p><?php _e($message)?></p>
    </div>
    <?php
}

function upload_export_file()
{
  if (isFreeMode()) return;

    if (empty($_FILES['breakdanceImportData'])) {
        admin_notice('Failed to upload a file', 'error');
        return;
    }

    /** @var array{ name:string, type:string, tmp_name:string, error:int, size: int } $import_file */
    $import_file = $_FILES['breakdanceImportData'];
    if ($import_file['error'] !== UPLOAD_ERR_OK) {
        admin_notice('Failed to upload a file', 'error');
        return;
    }
    $import_data = (string) file_get_contents($import_file['tmp_name']);

    /** @var list<array{option_name:string, option_value:string}> $config */
    $config = (array) json_decode($import_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        admin_notice('Invalid export data', 'error');
        return;
    }

    $import_was_successful = \Breakdance\Data\import_global_options($config);
    if (!$import_was_successful) {
        admin_notice('Error importing settings', 'error');
        return;
    }

    admin_notice('Settings imported');
}

function replace_urls()
{
    $from = (string) filter_input(INPUT_POST, 'from');
    $to   = (string) filter_input(INPUT_POST, 'to');
    $affectedValues = \Breakdance\Setup\replaceUrls($from, $to);

    if (is_wp_error($affectedValues)) {
        /** @psalm-suppress PossiblyInvalidMethodCall */
        admin_notice($affectedValues->get_error_message(), 'error');
        return;
    }

    /** @var array{postMeta: string, preferences: boolean} $affectedValues */
  $affectedValues = $affectedValues;

    /** @psalm-suppress PossiblyInvalidArgument */
    admin_notice(sprintf("%s rows affected.", $affectedValues['postMeta']));

    //  always regenerate fonts, even if no replace was done
    // a user may have used a tool like "Search And Replace" to update all their URLs
    // and then run this tool for fonts, or just to verify everything was replaced
    /** @psalm-suppress UndefinedFunction
      * @var array{error?: string} $fontFilesRegenerated
     */
    $fontFilesRegenerated = \Breakdance\CustomFonts\regenerateFontFiles();

    if (isset($fontFilesRegenerated['error'])) {
      admin_notice("Error regenerating font files: " . $fontFilesRegenerated['error']);
    }
}

function settings_page()
{
    ?>
    <h2>Tools</h2>
    <?php
    if (is_post_request()) {
        $mode = (string) filter_input(INPUT_POST, 'mode');

        switch ($mode) {
            case 'soft_reset':
                check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_SOFT_RESET);
                refresh();
                admin_notice('Soft Reset Succeeded');

                break;
            case 'total_reset':
                check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_TOTAL_RESET);
                reset();
                install();
                admin_notice('Total Reset Succeeded');
                break;
            case 'create_dirs':
                check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_CREATE_DIRECTORIES);

                $maybe_wp_error = try_to_create_all_required_directories();
                if (is_wp_error($maybe_wp_error)) {
                    /**
                     * @var \WP_Error $maybe_wp_error
                     */
                    $maybe_wp_error = $maybe_wp_error;
                    admin_notice('Failed to create Breakdance directories. ' . generate_error_msg_from_fs_wp_error($maybe_wp_error), 'error');
                } else {
                    admin_notice('All Breakdance directories have been successfully created.');
                }
                break;
            case 'upload_export_file':
                check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_UPLOAD_EXPORT_FILE);
                upload_export_file();
                break;
            case 'replace_url':
                check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_REPLACE_URL);
                replace_urls();
                break;
        }
    }

    /** @var array<string, string> $erroneous_directory_paths */
    $erroneous_directory_paths = array_filter(check_all_required_directories(), function ($maybe_fs_error) {
        return $maybe_fs_error !== null;
    });

    $disabledBecauseOfFreeMode = isFreeMode() ? "disabled" : '';
    ?>

    <table class="form-table" role="presentation">
        <tbody>
            <?php
            $regencache = (string) ($_GET['regencache'] ?? '');
            $highlightRegencache = ($regencache) && !is_post_request();
            ?>
            <tr <?php echo $highlightRegencache ? 'class="breakdance-admin-highlight-row"' : ''; ?>>
                <th scope="row">
                    Regenerate CSS Cache
                </th>
                <td>
                    <iframe id='settings-tools-regenerate-cache-iframe' width="100%" frameborder="0" src='<?php echo site_url("?breakdance=regenerate-cache") ?>'>
                    </iframe>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    Soft Reset
                </th>
                <td>

                    <form class='breakdance-are-you-sure-form' action="" method="post">
                        <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_SOFT_RESET); ?>
                        <button type="submit" class="button" name="mode" value="soft_reset">Soft Reset</button>
                    </form>

                    <p class="description">
                        Reset your Preferences and Icon Sets to factory defaults.
                    </p>

                </td>
            </tr>
            <tr>
                <th scope="row">
                    Total Reset
                </th>
                <td>

                    <form class='breakdance-are-you-sure-form' action="" method="post">
                        <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_TOTAL_RESET); ?>
                        <button type="submit" class="button" name="mode" value="total_reset">Total Reset</button>
                    </form>

                    <p class='description'>
                        Reset your entire Breakdance installation to factory defaults. Content you created in
                        Breakdance must still be deleted manually.
                    </p>
                </td>
            </tr>
            <tr id="create_directories_row">
                <th scope="row">
                    Create Directories
                </th>
                <td>
                    <form action="#" method="post">
                        <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_CREATE_DIRECTORIES); ?>
                        <button type="submit" class="button" name="mode" value="create_dirs">Create Directories</button>
                    </form>
                    <p class='description'>
                        Create the directories on your server that are required for Breakdance to function properly.</p>
                    <?php
if (sizeof($erroneous_directory_paths)): ?>
                        <p class='description'>
                            Existing problems:
                        <dl>
                            <?php
foreach ($erroneous_directory_paths as $directory_path => $fs_error): ?>
                                <dt><code><?=$directory_path?></code>:</dt>
                                <dd style="color: red;">
                                    <strong><?=$fs_error;?></strong>
                                </dd>
                            <?php
endforeach;?>
                        </dl>
                        </p>
                    <?php
endif;?>
                </td>
            </tr>
            <?php
                $fromUrl = (string) filter_input(INPUT_GET, 'from', FILTER_VALIDATE_URL);
                $toUrl = (string) filter_input(INPUT_GET, 'to', FILTER_VALIDATE_URL);
                $highlightReplaceTool = ($fromUrl || $toUrl) && !is_post_request();
            ?>
            <tr <?php echo $highlightReplaceTool ? 'class="breakdance-admin-highlight-row"' : ''; ?>>
                <th scope="row">
                    Replace URL
                </th>
                <td>
                    <form action="" method="post">
                        <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_REPLACE_URL); ?>
                        <input type="url" name="from" placeholder="http://old-url.com" required value="<?php echo esc_attr($fromUrl); ?>">
                        <input type="url" name="to" placeholder="http://new-url.com" required value="<?php echo esc_attr($toUrl); ?>">
                        <button class="button" name="mode" value="replace_url">Replace URL</button>
                    </form>
                    <p class="description">It is strongly recommended that you backup your database before running this tool.</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    Export Settings
                </th>
                <td>
                    <form action="" method="post">
                        <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_DOWNLOAD_EXPORT_FILE); ?>
                        <button type="submit" class="button" name="mode" value="download_export_file" <?= $disabledBecauseOfFreeMode ?>>Download Export File</button>
                    </form>
                    <p class='description'>
                        Export your Settings, Preferences, and Icon Sets to a JSON file.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    Import Settings
                </th>
                <td>
                    <form class='breakdance-are-you-sure-form wp-upload-form' enctype="multipart/form-data" action="" method="post">
                        <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_UPLOAD_EXPORT_FILE); ?>
                        <input type="file" accept="application/json" name="breakdanceImportData" <?= $disabledBecauseOfFreeMode ?> />
                        <button type="submit" class="button" name="mode" value="upload_export_file" <?= $disabledBecauseOfFreeMode ?>>Upload Export JSON File</button>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>

    <script type="text/javascript" src="<?php
echo BREAKDANCE_PLUGIN_URL; ?>plugin/lib/iframe-resizer@4/iframeResizer.min.js"></script>
    <script>
        iFrameResize({
        }, "#settings-tools-regenerate-cache-iframe");
    </script>


    <script>
        jQuery('.breakdance-are-you-sure-form').submit(function () {
            return confirm("Overwrite all your settings? This can't be undone without a backup.");
        });
    </script>

    </form>


    <?php
}


// handle the file download in the init hook so we can send headers
add_action('init', function () {
    if (!is_post_request() || isFreeMode()) {
        return;
    }

    $mode = (string) filter_input(INPUT_POST, 'mode');
    if ($mode === 'download_export_file') {
        check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_DOWNLOAD_EXPORT_FILE);

        $data_for_export = \Breakdance\Data\get_global_options_for_export();
        $filename = 'breakdance_settings_' . date('Y-m-d');
        // Force download .json file with exportData in it
        header("Content-type: application/vnd.ms-excel");
        header("Content-Type: application/force-download");
        header("Content-Type: application/download");
        header("Content-disposition: " . $filename . ".json");
        header("Content-disposition: filename=" . $filename . ".json");

        print json_encode($data_for_export);
        exit;
    }
});
