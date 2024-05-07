<?php

namespace Breakdance\ElementStudio;

require_once __DIR__ . '/save.php';
require_once __DIR__ . '/load.php';
require_once __DIR__ . '/delete.php';

/**
 * @param  string  $slug
 * @param  string  $directoryPath
 *
 * @return void
 */
function registerElementForEditing($slug, $directoryPath)
{
    /**
     * @psalm-suppress UndefinedConstant
     */
    ElementStudioController::getInstance()->elements[] = [
        'slug' => $slug,
        'directoryPath' => $directoryPath,
    ];
}

/**
 * @param  string  $directoryPath - e.g: $directoryPath = getDirectoryPathRelativeToPluginFolder(__DIR__) . '/elements'
 * @param  string  $namespace
 * @param 'element'|'macro'|'preset' $type
 * @param string $label
 * @param boolean $onlyForAdvancedUsers
 * @param boolean $excludeFromElementStudio
 *
 * @return void
 *
 */
function registerSaveLocation($directoryPath, $namespace, $type, $label, $onlyForAdvancedUsers, $excludeFromElementStudio = false)
{
    $saveLocation = [
        'directoryPath' => $directoryPath,
        'namespace' => $namespace,
        'type' => $type,
        'label' => $label,
        'onlyForAdvancedUsers' => $onlyForAdvancedUsers
     ];

    // useful to load manual elements without allowing users to save there
    if ($excludeFromElementStudio){
        $saveLocation['excludeFromElementStudio'] = true;
    }

    ElementStudioController::getInstance()->saveLocations[] = $saveLocation;
}

class ElementStudioController
{

    use \Breakdance\Singleton;

    /** @var array{slug:string,directoryPath:string}[] */
    public $elements = [];

    /** @var array{directoryPath:string, namespace: string, label: string, type: string, onlyForAdvancedUsers: boolean, excludeFromElementStudio?: boolean}[] */
    public $saveLocations = [];

}
