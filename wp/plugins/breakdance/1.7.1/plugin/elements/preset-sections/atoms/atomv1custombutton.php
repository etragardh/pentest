<?php

namespace Breakdance\Elements\PresetSections;

add_action('init', function () {

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $controls = \EssentialElements\AtomV1CustomButtonDesign::designControls();

    $atomV1CustomButtonDesign = reset($controls);

    if ($atomV1CustomButtonDesign) {
        PresetSectionsController::getInstance()->register(
            "EssentialElements\\AtomV1CustomButtonDesign",
            $atomV1CustomButtonDesign,
            true,
            [
                'relativePropertyPathsToWhitelistInFlatProps' => ['styles.size.full_width_at', 'styles'],
            ]
        );
    }

    if ($atomV1CustomButtonDesign) {
        /**
         * Removes `full_width_at`
         * @psalm-suppress MixedArrayAccess
         */
        unset($atomV1CustomButtonDesign['children'][0]['children']['3']);
        /**
         * Removes `override_width`
         * @psalm-suppress MixedArrayAccess
         */
        unset($atomV1CustomButtonDesign['children'][0]['children']['2']);
        /**
         * @psalm-suppress MixedArrayAssignment
         * @psalm-suppress MixedAssignment
         * @psalm-suppress MixedArrayAccess
         * @psalm-suppress MixedArgument
         * @psalm-suppress PossiblyUndefinedIntArrayOffset
         */
        $atomV1CustomButtonDesign['children'][0]['children'] = array_values($atomV1CustomButtonDesign['children'][0]['children']);

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\AtomV1CustomButtonDesignNoResponsive",
            $atomV1CustomButtonDesign,
            true,
            [
                'relativePropertyPathsToWhitelistInFlatProps' => ['styles.size.full_width_at', 'styles'],
            ]
        );
    }
});
