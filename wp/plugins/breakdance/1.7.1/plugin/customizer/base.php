<?php

namespace Breakdance\Customizer;

use WP_Customize_Manager;

use function \Breakdance\Admin\get_browse_mode_url_with_return_back_to_current_page;

add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    if (!\Breakdance\Permissions\hasMinimumPermission("full")) return;

    class BreakdanceCustomizeBrowseModeButtonControl extends \WP_Customize_Control
    {
        public $type = 'brekdance_browse_mode_button';

        public function enqueue()
        {
            parent::enqueue();
            wp_enqueue_style('breakdance-launcher-shared', BREAKDANCE_PLUGIN_URL . 'plugin/admin/launcher/css/shared.css');

            $js = <<<JS
                window.breakdanceCustomizeBrowseModeHandler = function(e) {
                    const browseModeOpenUrl = wp?.customize?.previewer?.previewUrl();
                    if (browseModeOpenUrl) {
                        e.preventDefault();
                        const url = new URL(e.target.href);
                        url.searchParams.append('browseModeOpenUrl', browseModeOpenUrl);
                        window.location.href = url.toString();
                    }
                }
            JS;

            wp_add_inline_script('customize-nav-menus', $js, 'after');
        }

        protected function render_content()
        {
            $browseModeUrl = get_browse_mode_url_with_return_back_to_current_page();
?>
            <div class="breakdance-customize-browse-mode-wrapper">
                <a id="breakdance-customize-browse-mode-btn" onclick="breakdanceCustomizeBrowseModeHandler(event)" class="breakdance-launcher-button" href="<?= $browseModeUrl; ?>" style="display: inline-block">Open Breakdance</a>
            </div>
<?php
        }
    }

    $wp_customize->add_section('breakdance_global_styles', array(
        'title' => 'Breakdance Global Styles',
        'priority' => 10000,
        'description_hidden' => true,
    ));

    $wp_customize->add_setting('breakdance_global_styles__browse', array(
        'default' => null,
    ));
    $wp_customize->add_control(
        new BreakdanceCustomizeBrowseModeButtonControl($wp_customize, 'breakdance_global_styles__browse', [
            'section' => 'breakdance_global_styles',
        ])
    );
});
