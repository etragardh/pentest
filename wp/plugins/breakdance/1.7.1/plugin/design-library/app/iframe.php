<?php

/**
 * @psalm-ignore-file
 */

use function Breakdance\Admin\get_env;
use function Breakdance\AJAX\get_nonce_for_ajax_requests;

require_once __DIR__ . "/../../loader/loader-utils.php";

$ajaxurl = admin_url('admin-ajax.php');
$envtype = get_env();

if ($envtype !== 'local') {
    $manifest = getProductionManifest(__DIR__ . '/../../../builder/dist', plugin_dir_url(__BREAKDANCE_PLUGIN_FILE__) . 'builder/dist');
}

$window_dot_breakdance_object_data = new stdClass();
$window_dot_breakdance_object_data->ajaxurl = $ajaxurl;
$window_dot_breakdance_object_data->ajaxnonce = get_nonce_for_ajax_requests();
$window_dot_breakdance_object_data->homeUrl = home_url();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Breakdance Templates</title>

    <script>
        // This one does not implement BreakdanceWindowObject
        window.Breakdance = <?= json_encode($window_dot_breakdance_object_data); ?>;
    </script>

    <?php

    if ($envtype === 'local') {
        echo getDevelopmentHeadLinks('design-library');
    } else {
        echo getProductionHeadLinks($manifest, 'design-library');
    }
    ?>
</head>

<body>
<div id="design-library-wrapper"></div>
<script type="text/javascript" src="<?php echo BREAKDANCE_PLUGIN_URL; ?>plugin/lib/iframe-resizer@4/iframeResizer.contentWindow.min.js"></script>

<?php
if ($envtype === 'local') {
    echo getDevelopmentFooterScripts('design-library');
} else {
    echo getProductionFooterScripts($manifest, 'design-library');
}
?>
</body>

</html>
