<?php

namespace Breakdance\Setup;

function reset()
{

    $option_names = \Breakdance\Data\get_all_option_names();

    foreach ($option_names as $option_name) {
        \Breakdance\Data\delete_global_option($option_name);
    }
}

function refresh()
{
    install();
}
