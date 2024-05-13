<?php
namespace Breakdance\Licensing;

use function Breakdance\Data\get_global_option;
use function Breakdance\Data\set_global_option;

/**
 * @param bool $should_receive
 * @return void
 */
function save_option_receive_beta_updates($should_receive)
{
    set_global_option('receive_beta_updates', (bool)$should_receive);
}

/**
 * @return bool
 */
function get_option_receive_beta_updates()
{
    return (bool)get_global_option('receive_beta_updates');
}
