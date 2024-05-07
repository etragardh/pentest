<?php

namespace Breakdance\GlobalDefaultStylesheets;

class GlobalDefaultStylesheetsController
{
    use \Breakdance\Singleton;

    /** @var string[] */
    public $stylesheetUrls = [];

    /**
     * @param string $stylesheetUrl
     */
    public function register($stylesheetUrl)
    {
        $this->stylesheetUrls[] = $stylesheetUrl;
    }

}
