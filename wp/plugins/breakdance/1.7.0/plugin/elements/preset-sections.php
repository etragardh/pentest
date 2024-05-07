<?php

namespace Breakdance\Elements\PresetSections;

use Breakdance\ElementStudio\ElementStudioController;
use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

/**
 *
 * @param string $presetSlug
 * @param false|string $label
 * @param false|string $sectionSlug
 * @param false|mixed $options
 * @return Control
 */
function getPresetSection($presetSlug, $label = false, $sectionSlug = false, $options = false)
{
    if (isset(PresetSectionsController::getInstance()->presets[$presetSlug])) {
        $presetSection = PresetSectionsController::getInstance()->presets[$presetSlug];
        $section = $presetSection['section'];
        $section['label'] = $label ?: $section['label'];
        $section['slug'] = $sectionSlug ?: $section['slug'];
        // sectionOptions must always exist in a preset
        /**
         * @psalm-suppress MixedArrayAssignment
         * @psalm-suppress MixedAssignment
         */
        $section['options']['sectionOptions']['type'] = $options['type'] ?? $section['options']['sectionOptions']['type'] ?? 'popout';

        if (isset($options['condition'])){
            /**
             * @psalm-suppress MixedArrayAccess
             * @psalm-suppress MixedArrayAssignment
             * @psalm-suppress MixedAssignment
             */
            $section['options']['condition'] = $options['condition'];
        }

        /**
         * @var Control
         */
        return $section;
    } else {
        return controlSection($sectionSlug ?: $presetSlug, $label ?: $presetSlug, [
            control('error', 'Error', [
                'type' => 'message',
                'messageOptions' => [
                    'text' => 'Preset section type "' . $presetSlug . '" does not exist',
                ],
            ]),
        ]);

        throw new \Exception('section preset type ' . $presetSlug . ' doesnt exist');
    }
}

/**
 * @return array{slug: string, directoryPath: string}[]
 */
function requirePresetsAndGetData()
{
    $savedLocations = ElementStudioController::getInstance()->saveLocations;
    $presetsSavedLocations = array_filter(
        $savedLocations,
        fn($location) => $location['type'] === 'preset'
    );

    $presetsData = [];

    foreach ($presetsSavedLocations as $savedLocation) {
        /**
         * @var string[] $presetsFilenames
         * @psalm-suppress UndefinedConstant
         */
        $presetsFilenames = glob(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $savedLocation['directoryPath'] . "/*.php");

        foreach ($presetsFilenames as $filename) {
            /** @psalm-suppress UnresolvableInclude */
            require_once $filename;

            $basename = str_replace('.php', '', basename($filename));

            $presetsData[] = [
                'slug' => $savedLocation['namespace'] . '\\' . $basename,
                'directoryPath' => $savedLocation['directoryPath']
            ];
        }
    }

    return $presetsData;
}

class PresetSectionsController
{
    use \Breakdance\Singleton;

    /**
     * @var Preset[]
     */
    public $presets = [];

    /**
     * @param string $slug
     * @param Control $section
     * @param boolean $availableInElementStudio
     * @param array{relativePropertyPathsToWhitelistInFlatProps?: string[], relativeDynamicPropertyPaths?: DynamicPropertyPath[], codeHelp?: string}|null $options
     */
    public function register(
        $slug,
        $section,
        $availableInElementStudio = false,
        $options = null
    ) {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-suppress PossiblyNullArrayAccess
         * @psalm-suppress MixedArrayAssignment
         * @psalm-suppress MixedArgument
         * @var array
         */
        $sectionOptions = array_key_exists('sectionOptions', $section['options']) ?
            $section['options']['sectionOptions'] :
            ['preset' => []];

        $presetOptions = [
            'preset' => [
                'slug' => $slug,
            ],
        ];

        if (isset($options['relativePropertyPathsToWhitelistInFlatProps'])) {
            $presetOptions['preset']['relativePropertyPathsToWhitelistInFlatProps'] = $options['relativePropertyPathsToWhitelistInFlatProps'];
        }

        if (isset($options['relativeDynamicPropertyPaths'])) {
            $presetOptions['preset']['relativeDynamicPropertyPaths'] = $options['relativeDynamicPropertyPaths'];
        }

        if (isset($options['codeHelp'])) {
            $presetOptions['preset']['codeHelp'] = $options['codeHelp'];
        }

        /**
         * @psalm-suppress MixedArrayAssignment
         */
        $section['options']['sectionOptions'] = array_merge(
            $sectionOptions,
            $presetOptions
        );

        $this->presets[$slug] = ['slug' => $slug, 'section' => $section, 'availableInElementStudio' => $availableInElementStudio];
    }

    /**
     * @return Preset[]
     */
    public function getAvailableInElementStudio()
    {
        return array_values(array_filter(
            $this->presets,
            function ($p) {
                return $p['availableInElementStudio'];
            }
        ));
    }
}
