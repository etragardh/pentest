<?php

namespace Breakdance;

/**
 * @return boolean
 */
function isRequestFromBuilderIframe()
{
    // "breakdance_iframe" is always added as a GET-parameter to the iframe URL by builder
    if (isset($_GET['breakdance_iframe']) && $_GET['breakdance_iframe']) {
        return true;
    } else {
        return false;
    }
}

/**
 * @return bool
 */
function isRequestFromBuilderSsr(){
    return filter_input(INPUT_POST, 'action') === 'breakdance_server_side_render';
}

/**
 * @return bool
 */
function isRequestFromBuilderDynamicDataGet(){
    return filter_input(INPUT_POST, 'action') === 'breakdance_dynamic_data_get';
}
