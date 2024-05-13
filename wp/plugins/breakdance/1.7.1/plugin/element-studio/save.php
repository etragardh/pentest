<?php

namespace Breakdance\ElementStudio;

use Breakdance\Filesystem\Consts;
use function Breakdance\Filesystem\clear_bucket_contents;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_save_element',
        'Breakdance\ElementStudio\save_element',
        'full',
        false,
        [
            'args' => [
                'php' => FILTER_UNSAFE_RAW,
                'html' => FILTER_UNSAFE_RAW,
                'css' => FILTER_UNSAFE_RAW,
                'defaultCss' => FILTER_UNSAFE_RAW,
                'directoryPath' => FILTER_UNSAFE_RAW,
                'bypassPsalm' => FILTER_UNSAFE_RAW,
            ],
            'optional_args' => ['bypassPsalm']
        ]
    );

    \Breakdance\AJAX\register_handler(
        'breakdance_save_macro',
        'Breakdance\ElementStudio\save_macro',
        'full',
        false,
        [
            'args' => [
                'macro' => FILTER_UNSAFE_RAW,
                'directoryPath' => FILTER_UNSAFE_RAW,
                'filename' => FILTER_UNSAFE_RAW,
            ],
        ]
    );

    \Breakdance\AJAX\register_handler(
        'breakdance_save_preset',
        'Breakdance\ElementStudio\save_preset',
        'full',
        false,
        [
            'args' => [
                'preset' => FILTER_UNSAFE_RAW,
                'directoryPath' => FILTER_UNSAFE_RAW,
                'filename' => FILTER_UNSAFE_RAW,
            ],
        ]
    );
});

/**
 * @param string $php
 * @param string $html
 * @param string $css
 * @param string $defaultCss
 * @param string $directoryPath
 * @param string|null $bypassPsalm
 * @return array
 */
function save_element($php, $html, $css, $defaultCss, $directoryPath, $bypassPsalm)
{
    $bypassPsalm = $bypassPsalm ?? 'no';

    /**
     * @var string
     * @psalm-suppress UndefinedConstant
     * @psalm-suppress MixedOperand
     */
    $directoryPath = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $directoryPath;

    if (!is_dir($directoryPath)) {
        mkdir($directoryPath, 0755, true);
    }

    // We use '/' instead of 'DIRECTORY_SEPARATOR' because otherwise someone
    // can create an element on Windows with a '\' and break it for Unix users
    // but Windows is fine using '/'
    $elementsSaved = file_put_contents($directoryPath . '/element.php', $php);
    $htmlSaved = file_put_contents($directoryPath . '/html.twig', $html);
    $cssSaved = file_put_contents($directoryPath . '/css.twig', $css);
    $defaultCssSaved = file_put_contents($directoryPath . '/default.css', $defaultCss);

    if ($bypassPsalm === 'yes') {
        file_put_contents($directoryPath . '/.ci-prepare-psalm-ignore', '');
    }

    // file_put_contents returns false
    if (
        $elementsSaved === false
        || $htmlSaved === false
        || $cssSaved === false
        || $defaultCssSaved === false
    ) {
        return [
            'error' => "Failed to create the files with the element's data. Directory used: {$directoryPath}",
        ];
    }

    return [];
}

/**
 * @param string $macro
 * @param string $directoryPath
 * @param string $filename
 * @return array
 */
function save_macro($macro, $directoryPath, $filename)
{
    /**
     * @var string
     * @psalm-suppress UndefinedConstant
     * @psalm-suppress MixedOperand
     */
    $directoryPath = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $directoryPath;

    if (!is_dir($directoryPath)) {
        mkdir($directoryPath, 0755, true);
    }

    // We use '/' instead of 'DIRECTORY_SEPARATOR' because otherwise someone
    // can create an element on Windows with a '\' and break it for Unix users
    // but Windows is fine using '/'
    $macroSaved = file_put_contents($directoryPath . '/' . $filename . '.twig', $macro);

    clear_bucket_contents(Consts::BREAKDANCE_FS_BUCKET_TWIG_CACHE);

    // file_put_contents returns false
    if ($macroSaved === false) {
        return [
            'error' => "Failed to save macro. Directory used: $directoryPath. Filename: $filename",
        ];
    }

    return [];
}

/**
 * @param string $preset
 * @param string $directoryPath
 * @param string $filename
 * @return array
 */
function save_preset($preset, $directoryPath, $filename)
{
    /**
     * @var string
     * @psalm-suppress UndefinedConstant
     * @psalm-suppress MixedOperand
     */
    $directoryPath = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $directoryPath;

    if (!is_dir($directoryPath)) {
        mkdir($directoryPath, 0755, true);
    }

    // We use '/' instead of 'DIRECTORY_SEPARATOR' because otherwise someone
    // can create an element on Windows with a '\' and break it for Unix users
    // but Windows is fine using '/'
    $macroSaved = file_put_contents($directoryPath . '/' . $filename . '.php', $preset);

    // file_put_contents returns false
    if ($macroSaved === false) {
        return [
            'error' => "Failed to save macro. Directory used: $directoryPath. Filename: $filename",
        ];
    }

    return [];
}
