<?php

namespace Breakdance\ElementStudio;

use function Breakdance\Elements\PresetSections\requirePresetsAndGetData;
use function Breakdance\Util\Timing\finish;
use function Breakdance\Util\Timing\start;

add_action('breakdance_loaded', function () {
    include_elements();

    \Breakdance\AJAX\register_handler('breakdance_load_element_studio', 'Breakdance\ElementStudio\loadData', 'full');

});

/**
 * @return array{'elements': array{slug:string,directoryPath:string}[], 'saveLocations': array{directoryPath:string}[]}
 */
function loadData()
{

    $saveLocationsForElementStudio = array_filter(
        \Breakdance\ElementStudio\ElementStudioController::getInstance()->saveLocations,
        fn($saveLocation) => !($saveLocation['excludeFromElementStudio'] ?? false)
    );

    $macros = array_map(
        function($macro) {
            return [
                'slug' => $macro['slug'],
                'directoryPath' => $macro['directoryPath'] ?? ''
            ];
        },
        \Breakdance\Elements\getTwigMacrosData(true)
    );

    return
        [
            'elements' => \Breakdance\ElementStudio\ElementStudioController::getInstance()->elements,
            'presets' => requirePresetsAndGetData(),
            'macros' => $macros,
            'saveLocations' => array_values($saveLocationsForElementStudio),
            'presetSections' => \Breakdance\Elements\PresetSections\PresetSectionsController::getInstance()->getAvailableInElementStudio(),
        ];
}

function include_elements()
{
    $timing = start('includeElements');

    $savedLocations = ElementStudioController::getInstance()->saveLocations;
    $elementsSaveLocations = array_filter(
        $savedLocations,
        fn($location) => $location['type'] === 'element'
    );

    $element_filenames = [];

    /**
     * @psalm-suppress UndefinedConstant
     * @var string
     */
    $wpPluginDir = WP_PLUGIN_DIR;

    foreach ($elementsSaveLocations as $savedLocation) {
        $element_filenames = array_merge(
            $element_filenames,
            glob($wpPluginDir . DIRECTORY_SEPARATOR . $savedLocation['directoryPath'] . "/*/*.php"),
        );
    }

    $ignored_suffix = 'ssr.php';

    foreach ($element_filenames as $filename) {
        $file_ignored = substr_compare($filename, $ignored_suffix, -strlen($ignored_suffix)) === 0;

        if ($file_ignored) {
            continue;
        }

        /**
         * @psalm-suppress UnresolvableInclude
         */
        require_once $filename;
    }

    finish($timing);
}
