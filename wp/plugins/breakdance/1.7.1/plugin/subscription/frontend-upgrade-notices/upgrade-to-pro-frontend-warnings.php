<?php

namespace Breakdance\Subscription;

/**
 * @param string $warning
 * @return void
 */
function logNoticeBecauseProOnlyFeatureWasUsed($warning) {
    if(!$warning) return;

    ProOnlyFeatureNoticesHolder::getInstance()->addWarning($warning);
}

class ProOnlyFeatureNoticesHolder
{
    use \Breakdance\Singleton;

    /**
     * @var string[]
     */
    public $warnings = [];

    /**
     * @param string $warning
     * @return void
     */
    public function addWarning($warning) {
        $this->warnings[] = $warning;
    }

}

add_action('breakdance_loaded', function () {
    if (freeModeOnFrontend() && \Breakdance\Permissions\hasMinimumPermission('edit')) {
        add_action('wp_body_open', function() {
            $warnings = ProOnlyFeatureNoticesHolder::getInstance()->warnings;

            if(!count($warnings)) return '';

            $upgradeToProWarnings = join("\n", ProOnlyFeatureNoticesHolder::getInstance()->warnings);


            $noticeTemplate = file_get_contents(__DIR__ . "/notice.html");

            $licenseKeyAdminScreenUrl = admin_url("admin.php?page=breakdance_settings&tab=license");

            $renderedNoticeTemplate = str_replace('%%UPGRADE_TO_PRO_WARNINGS%%', $upgradeToProWarnings, $noticeTemplate);
            $renderedNoticeTemplate = str_replace('%%LICENSE_KEY_ADMIN_SCREEN%%', $licenseKeyAdminScreenUrl, $renderedNoticeTemplate);


            echo $renderedNoticeTemplate;
        });
    }
});
