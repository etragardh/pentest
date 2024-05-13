<?php

namespace Breakdance\Elements\PresetSections;

add_action('init', function () {

    /**
     * @var Control[]
     * @psalm-suppress UndefinedClass
     */
    $controls = \EssentialElements\Formdesignoptions::designControls();

    $atomV1FormDesign = reset($controls);
    $atomV1FormDesignWoo = reset($controls);

    if ($atomV1FormDesign) {
        PresetSectionsController::getInstance()->register(
            "EssentialElements\\AtomV1FormDesign",
            $atomV1FormDesign,
            true
        );

    }

    if ($atomV1FormDesign) {
        /**
         * Removes `layout`
         * @psalm-suppress MixedArrayAccess
         */
        unset($atomV1FormDesign['children'][0]);
        /**
         * Removes `submit_button/full_width_at`
         * Removes `submit_button/override_width`
         * Removes `fields/advanced/responsive`
         * @psalm-suppress MixedArrayAccess
         */
        unset($atomV1FormDesign['children'][5]['children'][1]['children'][0]['children'][2]);
        /**
         * @psalm-suppress MixedArrayAccess
         */
        unset($atomV1FormDesign['children'][5]['children'][1]['children'][0]['children'][3]);

        /**
         * Removes `fields/advanced/responsive`
         * @psalm-suppress MixedArrayAccess
         */
        unset($atomV1FormDesign['children'][1]['children'][6]['children'][0]);

        /**
         * Removes `fields/advanced/responsive`
         * @psalm-suppress MixedArrayAccess
         */
        unset($atomV1FormDesign['children'][1]['children'][6]['children'][3]);

        /**
         * @psalm-suppress MixedArrayAssignment
         * @psalm-suppress MixedAssignment
         * @psalm-suppress MixedArrayAccess
         * @psalm-suppress MixedArgument
         * @psalm-suppress PossiblyUndefinedIntArrayOffset
         */
        $atomV1FormDesign['children'][1]['children'][6]['children'] = array_values($atomV1FormDesign['children'][1]['children'][6]['children']);

        /**
         * @psalm-suppress MixedArrayAssignment
         * @psalm-suppress MixedAssignment
         * @psalm-suppress MixedArrayAccess
         * @psalm-suppress MixedArgument
         * @psalm-suppress PossiblyUndefinedIntArrayOffset
         */
        $atomV1FormDesign['children'] = array_values($atomV1FormDesign['children']);

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\AtomV1FormDesignGlobalSettings",
            $atomV1FormDesign,
            true,
            [
                'relativePropertyPathsToWhitelistInFlatProps' => [],
            ]
        );
    }

    if ($atomV1FormDesignWoo) {
        /**
         * Removes `layout`, `messages`, `submit_button`, `notices`,
         * @psalm-suppress MixedArrayAccess
         */
        unset($atomV1FormDesignWoo['children'][0]);
        unset($atomV1FormDesignWoo['children'][3]);
        unset($atomV1FormDesignWoo['children'][4]);
        unset($atomV1FormDesignWoo['children'][5]);

        /**
         * Removes `fields/advanced/hide_labels`
         * @psalm-suppress MixedArrayAccess
         */
        unset($atomV1FormDesignWoo['children'][1]['children'][6]['children'][0]);

        /**
         * Removes `fields/advanced/radio_checkbox`
         * @psalm-suppress MixedArrayAccess
         */
        unset($atomV1FormDesignWoo['children'][1]['children'][6]['children'][1]);

        /**
         * Removes `fields/advanced/file_input`
         * @psalm-suppress MixedArrayAccess
         */
        unset($atomV1FormDesignWoo['children'][1]['children'][6]['children'][2]);

        /**
         * Removes `fields/advanced/responsive`
         * @psalm-suppress MixedArrayAccess
         */
        unset($atomV1FormDesignWoo['children'][1]['children'][6]['children'][4]);

        /**
         * @psalm-suppress MixedArrayAssignment
         * @psalm-suppress MixedAssignment
         * @psalm-suppress MixedArrayAccess
         * @psalm-suppress MixedArgument
         * @psalm-suppress PossiblyUndefinedIntArrayOffset
         */
        $atomV1FormDesignWoo['children'][1]['children'][6]['children'] = array_values($atomV1FormDesignWoo['children'][1]['children'][6]['children']);

        /**
         * @psalm-suppress MixedArrayAssignment
         * @psalm-suppress MixedAssignment
         * @psalm-suppress MixedArrayAccess
         * @psalm-suppress MixedArgument
         * @psalm-suppress PossiblyUndefinedIntArrayOffset
         */
        $atomV1FormDesignWoo['children'] = array_values($atomV1FormDesignWoo['children']);

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\AtomV1FormDesignWoo",
            $atomV1FormDesignWoo,
            true
        );
    }

});
