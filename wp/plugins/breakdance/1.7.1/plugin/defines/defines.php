<?php

namespace Breakdance\Defines;

$plugin_path_parts = explode('/', plugin_basename(__FILE__));
$plugin_name = $plugin_path_parts[0];

define('BREAKDANCE_PLUGIN_URL', plugins_url() . '/' . $plugin_name . '/');
