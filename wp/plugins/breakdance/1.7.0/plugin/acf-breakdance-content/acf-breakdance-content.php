<?php

class AcfBreakdanceContent
{
    /**
     * @var array{path: string, url: string, version: string}
     */
    public $settings;

    function __construct()
    {
        $this->settings = [
            'version' => '1.0.0',
            'url' => plugin_dir_url(__FILE__),
            'path' => plugin_dir_path(__FILE__)
        ];

        // add_action('acf/include_field_types', [$this, 'include_field']);
    }

    /**
     * @param string|false $version
     * @return void
     */
    function include_field($version = false)
    {
        require_once(__DIR__ . '/acf-breakdance-content-field.php');
    }

}

new AcfBreakdanceContent();
