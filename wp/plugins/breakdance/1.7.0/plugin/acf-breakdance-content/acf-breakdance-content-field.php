<?php

if (!class_exists('acf_field')) {
    return;
}

/**
 * @psalm-suppress UndefinedClass
 */
class AcfBreakdanceContentField extends acf_field
{
    /**
     * @var array{path: string, url: string, version: string}
     */
    public array $settings;

    /**
     * @param string[] $settings
     */
    function __construct($settings)
    {

        $this->name = 'breakdance_content';

        $this->label = "Breakdance Content";

        $this->category = 'content';

        $this->settings = $settings;

        parent::__construct();

    }

    /**
     * @param AcfField $field
     * @return void
     */
    function render_field($field)
    {
        $postId = (string)get_the_ID();
        $fieldName = $field['_name'];

        echo "<span class='spinner'></span>";

        $acfBlockId = get_field($fieldName, $postId, false);
        if ($acfBlockId) {
            $builderLoaderUrl = \Breakdance\Admin\get_builder_loader_url($acfBlockId);
            echo "<a class='breakdance-launcher-small-button breakdance-content-area--add-block hidden' href='#' data-field='{$fieldName}' data-post-id='{$postId}'>Edit in Breakdance</a>";
            echo "<a class='breakdance-launcher-small-button breakdance-content-area--edit-block' href='{$builderLoaderUrl}'>Edit in Breakdance</a>";
            echo "<a class='breakdance-launcher-link breakdance-content-area--remove-block' href='#' data-field='{$fieldName}' data-post-id='{$postId}'>Clear</a>";
        } else {
            echo "<a class='breakdance-launcher-small-button breakdance-content-area--add-block' href='#' data-field='{$fieldName}' data-post-id='{$postId}'>Edit in Breakdance</a>";
        }
    }

    /**
     * @param $value
     * @param $postId
     * @param $field
     * @return false|string
     * @throws Exception
     */
    function format_value($value, $postId, $field)
    {
        return Breakdance\Render\render((int)$value);
    }

    function input_admin_enqueue_scripts()
    {

        $url = $this->settings['url'] . 'acf-breakdance-content.js';

        wp_register_script('breakdance-launcher-shared', BREAKDANCE_PLUGIN_URL . 'plugin/admin/launcher/js/shared.js');
        wp_enqueue_style('breakdance-launcher-shared', BREAKDANCE_PLUGIN_URL . 'plugin/admin/launcher/css/shared.css');

        wp_register_script('breakdance-content-area', $url, ['acf-input']);
        wp_enqueue_script('breakdance-content-area');
    }
}

/**
 * @psalm-suppress MissingDependency
 */
new AcfBreakdanceContentField($this->settings);
