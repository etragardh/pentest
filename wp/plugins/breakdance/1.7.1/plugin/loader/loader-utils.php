<?php

// @psalm-ignore-file

function getProductionManifest(string $relativePathToDist, $urlPath = false)
{
    $manifest = json_decode(file_get_contents("{$relativePathToDist}/manifest.json"), true);

    /*
     * Looks like: ["app.js" => "/js/app.dd8c7caa.js"]
     * @var array{string, string}
     */
    $manifestWithCorrectUrls = [];

    foreach ($manifest as $name => $url) {
        $url = str_replace("//breakdance.local:8080/", "/", $url);

        $manifestWithCorrectUrls[$name] = $urlPath ? $urlPath . $url : $relativePathToDist . $url;
    }

    return $manifestWithCorrectUrls;
}

function getProductionHeadLinks($manifest, $appName)
{
    ob_start();

    if (key_exists("favicon.svg", $manifest)) {
        ?>
        <link rel="icon" class="js-site-favicon" type="image/svg+xml" href="<?php echo $manifest["favicon.svg"]; ?>" media="(prefers-color-scheme: light)">
        <link rel="icon" class="js-site-favicon" type="image/svg+xml" href="<?php echo $manifest["favicon-dark.svg"]; ?>" media="(prefers-color-scheme: dark)">
        <?php
    }

    if (key_exists("{$appName}.css", $manifest)) {
        ?>
        <link href="<?php echo $manifest["{$appName}.css"] ?>" rel="stylesheet">
        <link href="<?php echo $manifest["{$appName}.css"] ?>" rel="preload" as="style">
        <?php
    }

    if (key_exists('chunk-vendors.css', $manifest)) {
        ?>
        <link href="<?php echo $manifest['chunk-vendors.css'] ?>" rel="preload" as="style">
        <link href="<?php echo $manifest['chunk-vendors.css'] ?>" rel=stylesheet>
        <?php
    }

    if (key_exists('chunk-common.css', $manifest)) {
        ?>
        <link href="<?php echo $manifest['chunk-common.css'] ?>" rel="preload" as="style">
        <link href="<?php echo $manifest['chunk-common.css'] ?>" rel=stylesheet>
        <?php
    }

    if (key_exists("{$appName}.js", $manifest)) {
        ?>
        <link href="<?php echo $manifest["{$appName}.js"] ?>" rel="preload" as="script">
        <?php
    }

    if (key_exists('chunk-vendors.js', $manifest)) {
        ?>
        <link href="<?php echo $manifest['chunk-vendors.js'] ?>" rel="preload" as="script">
        <?php
    }

    if (key_exists('chunk-common.js', $manifest)) {
        ?>
        <link href="<?php echo $manifest['chunk-common.js'] ?>" rel="preload" as="script">
        <?php
    }

    return ob_get_clean();

}

function getProductionFooterScripts($manifest, $appName)
{
    ob_start();

    if (key_exists("{$appName}.js", $manifest)) {
        ?>
        <script src="<?php echo $manifest["{$appName}.js"] ?>"></script>
        <?php
    }

    if (key_exists('chunk-vendors.js', $manifest)) {
        ?>
        <script src="<?php echo $manifest['chunk-vendors.js'] ?>"></script>
        <?php
    }

    if (key_exists('chunk-common.js', $manifest)) {
        ?>
        <script src="<?php echo $manifest['chunk-common.js'] ?>"></script>
        <?php
    }

    return ob_get_clean();

}

function getDevelopmentHeadLinks($appName){
    ob_start();
    ?>
    <link rel="icon" class="js-site-favicon" type="image/svg+xml" href="//breakdance.local:8080/favicon.svg" media="(prefers-color-scheme: light)">
    <link rel="icon" class="js-site-favicon" type="image/svg+xml" href="//breakdance.local:8080/favicon-dark.svg" media="(prefers-color-scheme: dark)">
    <link href="//breakdance.local:8080/js/chunk-vendors.js" rel="preload" as="script">
    <link href="//breakdance.local:8080/js/chunk-common.js" rel="preload" as="script">
    <link href="//breakdance.local:8080/js/<?php echo $appName?>.js" rel="preload" as="script">
    <?php

    return ob_get_clean();
}

function getDevelopmentFooterScripts($appName){
    ob_start();
    ?>
    <script type="text/javascript" src="//breakdance.local:8080/js/chunk-vendors.js"></script>
    <script type="text/javascript" src="//breakdance.local:8080/js/chunk-common.js"></script>
    <script type="text/javascript" src="//breakdance.local:8080/js/<?php echo $appName ?>.js"></script>
    <?php
    return ob_get_clean();

}
