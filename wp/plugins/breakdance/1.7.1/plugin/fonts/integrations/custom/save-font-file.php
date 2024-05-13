<?php

namespace Breakdance\CustomFonts;

use Breakdance\Filesystem\Consts;

use function Breakdance\Filesystem\HelperFunctions\generate_error_msg_from_fs_wp_error;
use function Breakdance\Filesystem\HelperFunctions\get_file_url;
use function Breakdance\Filesystem\HelperFunctions\is_fs_error;
use function Breakdance\Filesystem\move_uploaded_file_to_bucket;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_save_font_file',
        'Breakdance\CustomFonts\save_font_file',
        'edit'
    );
});

function save_font_file()
{
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $outputFileBasename = str_replace([' ', '-'], '_', $file['name']);
        $writeErrorOrFilename = move_uploaded_file_to_bucket(Consts::BREAKDANCE_FS_BUCKET_FONTS, $_FILES['file'], $outputFileBasename);

        if (!is_fs_error($writeErrorOrFilename)) {
            $response = [
                'fileUrl' => get_file_url(Consts::BREAKDANCE_FS_BUCKET_FONTS, $writeErrorOrFilename),
                'fileName' => $writeErrorOrFilename
            ];
        } else {
            $response = ['error' => generate_error_msg_from_fs_wp_error($writeErrorOrFilename)];
        }
    } else {
        $response = ['error' => 'No file was uploaded'];
    }

    return $response;
}
