<?php

namespace Breakdance\Themeless;

class PopupController  {
    use \Breakdance\Singleton;

    /**
     * @var int[]
     */
    public $popups = [];

    /**
     * @var int[]
     */
    public $rendered = [];

    /**
     * @param int | string $popupId
     * @return void
     */
    public function registerPopup($popupId)
    {
        if ($popupId && !in_array($popupId, $this->popups)) {
            $this->popups[] = (int) $popupId;
        }
    }

    /**
     * @param int | string $popupId
     * @return void
     */
    public function markAsRendered($popupId)
    {
        $this->rendered[] = (int) $popupId;
    }

    /**
     * @return int[]
     */
    public function getPopupsThatHaveNotAlreadyBeenRendered()
    {
        return array_filter($this->popups, function($popupId) {
            return !in_array($popupId, $this->rendered, true);
        });
    }
}
