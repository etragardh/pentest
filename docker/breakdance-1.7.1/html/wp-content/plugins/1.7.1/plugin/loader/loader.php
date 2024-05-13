<?php


use function Breakdance\Admin\get_env;

$mode = (string) ($_GET['mode'] ?? 'builder');

require_once __DIR__ . "/loader-utils.php";

$ajaxurl = admin_url('admin-ajax.php');
$envtype = get_env();

if ($envtype !== 'local') {
    $manifest = getProductionManifest(__DIR__ . '/../../builder/dist', plugin_dir_url(__BREAKDANCE_PLUGIN_FILE__) . 'builder/dist');
}

$window_dot_breakdance_object_data = new stdClass();
$window_dot_breakdance_object_data->ajaxurl = $ajaxurl;
$window_dot_breakdance_object_data->ajaxnonce = \Breakdance\AJAX\get_nonce_for_ajax_requests();
$window_dot_breakdance_object_data->subscriptionMode = \Breakdance\Subscription\getSubscriptionMode();

if ($mode === 'builder') {
    if (!isset($_GET['id'])) {
        die('error');
    }
    $id = $_GET['id'];

    $window_dot_breakdance_object_data->initialDocumentToLoadId = (int) $id;
    $window_dot_breakdance_object_data->initialMode = 'builder';
} elseif ($mode === 'browse') {
    $window_dot_breakdance_object_data->initialMode = 'browse';

    $returnUrl = filter_input(INPUT_GET, 'returnUrl', FILTER_VALIDATE_URL);
    if ($returnUrl) {
        $window_dot_breakdance_object_data->returnUrl = $returnUrl;
    }

    $browseModeOpenUrl = filter_input(INPUT_GET, 'browseModeOpenUrl', FILTER_VALIDATE_URL);
    if ($browseModeOpenUrl) {
        $window_dot_breakdance_object_data->browseModeOpenUrl = $browseModeOpenUrl;
    }
}
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <title>Breakdance</title>

    <script>
        window.Breakdance = <?= json_encode($window_dot_breakdance_object_data); ?>;
    </script>

    <?php
    if ($envtype === 'local') {
        echo getDevelopmentHeadLinks('app');
    } else {
        echo getProductionHeadLinks($manifest, 'app');
    }
    ?>
</head>

<body>
<div id="app"></div>
<?php
if ($envtype === 'local') {
    echo getDevelopmentFooterScripts('app');
} else {
    echo getProductionFooterScripts($manifest, 'app');
}
?>
</body>

</html>

