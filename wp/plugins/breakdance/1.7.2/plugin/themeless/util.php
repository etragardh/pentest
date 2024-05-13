<?php

namespace Breakdance\Themeless;

use function Breakdance\Themeless\ThemeDisabler\is_theme_disabled;
use function Breakdance\Themeless\ThemeDisabler\is_zero_theme_enabled;

function outputHeadHtml() {

$isThemelessOrZeroTheme = is_theme_disabled() || is_zero_theme_enabled();

?>
<!doctype html>
<html <?php language_attributes();?>>
<head>
    <meta charset="<?php bloginfo('charset');?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if (is_theme_disabled() || is_zero_theme_enabled()) : ?>
        <link rel='stylesheet' href='<?php echo BREAKDANCE_PLUGIN_URL . "plugin/themeless/normalize.min.css"; ?>'>
    <?php endif ?>
    <?php wp_head();?>
</head>
<body <?php body_class($isThemelessOrZeroTheme ? ['breakdance'] : []);?>>
    <?php wp_body_open();?>
    <?php
}

function outputFootHtml() {
    ?>
    <?php wp_footer();?>
    </body>
    </html>
    <?php
}

?>
