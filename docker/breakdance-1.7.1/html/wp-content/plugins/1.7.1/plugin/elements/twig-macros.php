<?php

namespace Breakdance\Elements;

use Breakdance\ElementStudio\ElementStudioController;

/**
 * @param boolean $forElementStudio
 * @return array{slug:string,macro:string,directoryPath?:string}[]
 */
function getTwigMacrosData($forElementStudio)
{
    $savedLocations = ElementStudioController::getInstance()->saveLocations;
    $macrosSavedLocations = array_filter(
        $savedLocations,
        fn($location) => $location['type'] === 'macro'
    );

    do_action('breakdance_register_macros');
    $macrosData = $forElementStudio ? [] : MacrosController::getInstance()->macros;

    /**
     * @psalm-suppress UndefinedConstant
     * @var string
     */
    $wpPluginDir = WP_PLUGIN_DIR;

    foreach ($macrosSavedLocations as $savedLocation) {
        $macroFilenames = glob($wpPluginDir . DIRECTORY_SEPARATOR . $savedLocation['directoryPath'] . "/*.twig");

        foreach ($macroFilenames as $filename) {
            $data = [
                'slug' => $savedLocation['namespace'] . '\\' . getMacroSlugFromFilename($filename),
                'macro' => (string)file_get_contents($filename),
            ];

            if($forElementStudio){
                $data['directoryPath'] = $savedLocation['directoryPath'];
            }

            $macrosData[] = $data;
        }
    }

    return $macrosData;
}

/**
 * @param string $filename
 * @return string
 */
function getMacroSlugFromFilename($filename)
{
    return str_replace('.twig', '', basename($filename));
}

/**
 * @return string
 */
function get_twig_macros_string()
{
    return join(
        "\n\n",
        array_map(
            function($macro) {
                return $macro['macro'];
            },
            getTwigMacrosData(false)
        ),
    );
}


class MacrosController
{
    use \Breakdance\Singleton;

    /**
     * @var array{slug:string,macro:string,directoryPath:string}[]
     */
    public $macros = [];

    /**
     * @param string $macro
     * @param string $slug
     * @param string $directoryPath
     */
    public function register($macro, $slug, $directoryPath)
    {
        $this->macros[] = [
            'slug' => $slug,
            'macro' => $macro,
            'directoryPath' => $directoryPath
        ];
    }

}
