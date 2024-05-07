<?php

namespace Breakdance\GlobalSettings;

/**
 * @return string
 */
function ATOMS_TEMPLATE()
{
    return (string) file_get_contents(dirname(__FILE__) . '/../../elements/atom-default-css/atom-default-css.css.twig');
}
