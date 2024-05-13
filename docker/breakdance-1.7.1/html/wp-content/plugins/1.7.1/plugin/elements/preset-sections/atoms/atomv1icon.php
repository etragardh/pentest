<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\removeHoverFromControlAndChildren;

add_action('init', function() {

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $controls = \EssentialElements\Icon::designControls();


    $atomV1IconDesignWithHover = reset($controls);

    if ($atomV1IconDesignWithHover) {
        $atomV1IconDesignNoHover = removeHoverFromControlAndChildren($atomV1IconDesignWithHover);

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\AtomV1IconDesignWithHover",
            $atomV1IconDesignWithHover,
            true
        );

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\AtomV1IconDesign",
            $atomV1IconDesignNoHover,
            true
        );
    }


});
