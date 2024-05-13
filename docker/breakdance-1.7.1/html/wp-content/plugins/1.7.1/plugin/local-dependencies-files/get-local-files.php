<?php

namespace Breakdance\AvailableDependencyFiles;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_local_dependency_files',
        'Breakdance\AvailableDependencyFiles\getLocalDependenciesFiles',
        'edit'
    );
});

/**
 * @return array
 */
function getLocalDependenciesFiles()
{
    /** @psalm-suppress UndefinedConstant */
    $dependenciesFilesFolder = __BREAKDANCE_ELEMENTS_DIR__ . '/dependencies-files';

    if (!is_dir($dependenciesFilesFolder)) {
        return [];
    }

    $dependenciesFoldersPath = glob("{$dependenciesFilesFolder}/*");

    $localFiles = array_map(function ($folderPath) use ($dependenciesFilesFolder) {
        if (!is_dir($folderPath)) return [];

        return [
            "name" => basename($folderPath),
            "files" => [
                // get files down to 2 levels deep
                'js' => getDependencyFilesFromFilePaths(glob("{$folderPath}/{,*/}*.js", GLOB_BRACE), $folderPath),
                'css' => getDependencyFilesFromFilePaths(glob("{$folderPath}/{,*/}*.css", GLOB_BRACE), $folderPath)
            ]
        ];
    },
        $dependenciesFoldersPath);

    return ['localDependencyFiles' => $localFiles];
}

/**
 * @param string[] $filePaths
 * @param string $folderPath
 * @return array{name: string, url: string}[]
 */
function getDependencyFilesFromFilePaths($filePaths, $folderPath)
{
    return array_map(function ($filePath) use ($folderPath) {
        $folderName = basename($folderPath);
        // e.g "styles.css", or "css/{styles.css}" if nested
        $relativeFilePath = str_replace("{$folderPath}/", '', $filePath);

        return [
            "name" => "{$relativeFilePath} ({$folderName})",
            // We can't hardcode "wp-content" or "plugin" as part of the URL because they're customizable
            "url" => "%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/{$folderName}/{$relativeFilePath}"
        ];
    },
        $filePaths
    );
}

/**
 * @return array<string, string>
 */
function getReusableDependenciesUrls()
{
    $urls = [
        'gsap' => 'https://unpkg.com/gsap@3.12.2/dist/gsap.min.js',
        'scrollTrigger' => 'https://unpkg.com/gsap@3.12.2/dist/ScrollTrigger.min.js'
    ];

    /**
     * @var array<string, string>
     */
    return apply_filters('breakdance_reusable_dependencies_urls', $urls);
}
