<?php

namespace Breakdance\Security;

/**
 * @return boolean
 */
function isPostTypePublicOrNot()
{
    /* someone could set breakdance=true in the query string to get access to this shit without auth */
    $permissions = \Breakdance\Permissions\hasMinimumPermission('edit');

    /* but admin users might want to preview on frontend */
    $adminUserTryingToPreviewOnFrontend = boolval($_GET['breakdance_preview'] ?? false);

    /* adding breakdance=true to the preview URL hides the admin bar... */

    /** @psalm-suppress RedundantCondition */
    if ($permissions && (\Breakdance\isRequestFromBuilderIframe() || $adminUserTryingToPreviewOnFrontend)) {
        return true;
    }

    return false;
}
