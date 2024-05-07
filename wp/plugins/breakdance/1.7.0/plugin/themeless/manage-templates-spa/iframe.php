<?php

/**
 * @psalm-ignore-file
 */


use function Breakdance\Admin\get_env;

require_once __DIR__ . "/../../loader/loader-utils.php";

$ajaxurl = admin_url('admin-ajax.php');
$envtype = get_env();

if ($envtype !== 'local') {
    $manifest = getProductionManifest(__DIR__ . '/../../../builder/dist', plugin_dir_url(__BREAKDANCE_PLUGIN_FILE__) . 'builder/dist');
}

$window_dot_breakdance_object_data = new stdClass();
$window_dot_breakdance_object_data->ajaxurl = $ajaxurl;
$window_dot_breakdance_object_data->ajaxnonce = \Breakdance\AJAX\get_nonce_for_ajax_requests();
$window_dot_breakdance_object_data->subscriptionMode = \Breakdance\Subscription\getSubscriptionMode();

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
        echo getDevelopmentHeadLinks('manage-templates');
    } else {
        echo getProductionHeadLinks($manifest, 'manage-templates');
    }
    ?>
</head>

<body>
<div id="manage-templates-wrapper"></div>

<?php
if ($envtype === 'local') {
    echo getDevelopmentFooterScripts('manage-templates');
} else {
    echo getProductionFooterScripts($manifest, 'manage-templates');
}
?>
</body>

</html>
