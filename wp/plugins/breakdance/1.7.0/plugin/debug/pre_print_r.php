<?php

namespace Breakdance\Debug;

/**
 * @param array $arg
 * @return void
 */
function pre_print_r($arg)
{
    echo "<pre>";
    print_r($arg);
    echo "</pre>";
}
