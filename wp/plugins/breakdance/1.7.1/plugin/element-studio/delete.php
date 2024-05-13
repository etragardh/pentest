<?php

namespace Breakdance\ElementStudio;

use function Breakdance\Filesystem\HelperFunctions\get_uploads_dir_abs_path;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_delete_element',
        'Breakdance\ElementStudio\delete_element',
        'full',
        false,
        ['args' => ['directoryPath' => FILTER_UNSAFE_RAW]]
    );

    \Breakdance\AJAX\register_handler(
        'breakdance_delete_macro',
        'Breakdance\ElementStudio\delete_macro',
        'full',
        false,
        ['args' => [
            'directoryPath' => FILTER_UNSAFE_RAW,
            'filename' => FILTER_UNSAFE_RAW
         ]]
    );

    \Breakdance\AJAX\register_handler(
        'breakdance_delete_preset',
        'Breakdance\ElementStudio\delete_preset',
        'full',
        false,
        ['args' => [
            'directoryPath' => FILTER_UNSAFE_RAW,
            'filename' => FILTER_UNSAFE_RAW
         ]]
    );
});

/**
 * @param string $directoryPathParam
 * @return array
 */
function delete_element($directoryPathParam)
{
    /**
     * @var string
     * @psalm-suppress UndefinedConstant
     * @psalm-suppress MixedOperand
     */
    $directoryPath = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $directoryPathParam;

    /**
     * @var string
     * @psalm-suppress MixedMethodCall
     */
    $isDeletedFirstAttemp = get_filesystem()->rmdir($directoryPath, true);
    if (!$isDeletedFirstAttemp) {
        /**
         * @psalm-suppress MixedOperand
         */
        return [
            'error' => "Failed to delete element in \"$directoryPathParam\"",
        ];
    }

    return ['data' => 'success'];
}

/**
 * @param string $directoryPath
 * @param string $slug
 * @return array
 */
function delete_macro($directoryPath, $slug)
{
    /**
     * @var string
     * @psalm-suppress UndefinedConstant
     * @psalm-suppress MixedOperand
     */
    $filePath = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $directoryPath . '/' . $slug . '.twig';

    /**
     * @var string
     * @psalm-suppress MixedMethodCall
     */
    $deletedFile = get_filesystem()->delete($filePath);
    if (!$deletedFile) {
        /**
         * @psalm-suppress MixedOperand
         */
        return [
            'error' => "Failed to delete macro $slug",
        ];
    }

    return ['data' => 'success'];
}

/**
 * @param string $directoryPath
 * @param string $slug
 * @return array
 */
function delete_preset($directoryPath, $slug)
{
    /**
     * @var string
     * @psalm-suppress UndefinedConstant
     * @psalm-suppress MixedOperand
     */
    $filePath = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $directoryPath . '/' . $slug . '.php';

    /**
     * @var string
     * @psalm-suppress MixedMethodCall
     */
    $deletedFile = get_filesystem()->delete($filePath);
    if (!$deletedFile) {
        /**
         * @psalm-suppress MixedOperand
         */
        return [
            'error' => "Failed to delete preset $slug",
        ];
    }

    return ['data' => 'success'];
}
