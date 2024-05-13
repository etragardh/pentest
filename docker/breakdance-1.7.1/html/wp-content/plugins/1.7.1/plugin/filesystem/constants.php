<?php

namespace Breakdance\Filesystem;

class Consts
{
    /**
     * Bucket IDs definitions
     */
    public const BREAKDANCE_FS_BUCKET_CSS = 'BREAKDANCE_FS_BUCKET_CSS';
    public const BREAKDANCE_FS_BUCKET_DESIGN_SETS = 'BREAKDANCE_FS_BUCKET_DESIGN_SETS';
    public const BREAKDANCE_FS_BUCKET_FONT_FAMILIES = 'BREAKDANCE_FS_BUCKET_FONT_FAMILIES';
    public const BREAKDANCE_FS_BUCKET_FONTS = 'BREAKDANCE_FS_BUCKET_FONTS';
    public const BREAKDANCE_FS_BUCKET_TWIG_CACHE = 'BREAKDANCE_FS_BUCKET_TWIG_CACHE';


    public const BREAKDANCE_FS_ERROR_MKDIR_FAILED = 'BREAKDANCE_FS_ERROR_MKDIR_FAILED';
    public const BREAKDANCE_FS_ERROR_REMOVAL_FAILED = 'BREAKDANCE_FS_ERROR_REMOVAL_FAILED';
    public const BREAKDANCE_FS_ERROR_FILE_CREATION_FAILED = 'BREAKDANCE_FS_ERROR_FILE_CREATION_FAILED';
    public const BREAKDANCE_FS_ERROR_UPLOAD_FAILED = 'BREAKDANCE_FS_ERROR_UPLOAD_FAILED';
    public const BREAKDANCE_FS_ERROR_UNABLE_TO_CREATE_WP_UPLOADS_BASEDIR = 'BREAKDANCE_FS_ERROR_UNABLE_TO_CREATE_WP_UPLOADS_BASEDIR';
    public const BREAKDANCE_FS_ERROR_DIRECTORY_NON_WRITABLE = 'BREAKDANCE_FS_ERROR_DIRECTORY_NON_WRITEABLE';
    public const BREAKDANCE_FS_ERROR_DIRECTORY_DOES_NOT_EXIST = 'BREAKDANCE_FS_ERROR_DIRECTORY_DOES_NOT_EXIST';
    public const BREAKDANCE_FS_ERROR_NOT_A_DIRECTORY = 'BREAKDANCE_FS_ERROR_NOT_A_DIRECTORY';
    public const BREAKDANCE_FS_ERROR_NOT_A_FILE = 'BREAKDANCE_FS_ERROR_NOT_A_FILE';
}


const BREAKDANCE_UPLOADS_SUBDIR_REL_PATH = 'breakdance';

$wp_installation_unique_hash = hash(
    "crc32b",
    join(
        '|',
        [
            // {@link \posix_getuid()} is unavailable under Windows
            function_exists('\posix_getuid') ? (string) posix_getuid() : '',
            (string) constant('ABSPATH')
        ]
    )
);

/**
 * Mapping of bucket IDs to their uploads directory subpaths, or to their absolute paths
 */
define("Breakdance\Filesystem\BREAKDANCE_FS_BUCKETS", [
    Consts::BREAKDANCE_FS_BUCKET_CSS => 'css',
    Consts::BREAKDANCE_FS_BUCKET_DESIGN_SETS => 'design_sets',
    Consts::BREAKDANCE_FS_BUCKET_FONT_FAMILIES => 'font_styles',
    Consts::BREAKDANCE_FS_BUCKET_FONTS => 'fonts',
    Consts::BREAKDANCE_FS_BUCKET_TWIG_CACHE => get_temp_dir()
        . "breakdance-{$wp_installation_unique_hash}/twig-auto-generated-cache",
]);

/**
 * @var array<FSError, string>
 */
const BREAKDANCE_FS_OPERATION_ERROR_MESSAGES = [
    Consts::BREAKDANCE_FS_ERROR_MKDIR_FAILED => 'Failed to create directory "%s".',
    Consts::BREAKDANCE_FS_ERROR_REMOVAL_FAILED => 'Failed to delete "%s".',
    Consts::BREAKDANCE_FS_ERROR_FILE_CREATION_FAILED => 'Failed to create file "%s".',
    Consts::BREAKDANCE_FS_ERROR_UPLOAD_FAILED => 'Failed to upload a file.',
    Consts::BREAKDANCE_FS_ERROR_UNABLE_TO_CREATE_WP_UPLOADS_BASEDIR => 'WordPress failed to create its uploads directory.',
    Consts::BREAKDANCE_FS_ERROR_DIRECTORY_NON_WRITABLE => 'Directory "%s" is not writable for PHP process. Try setting its permissions to "0777" manually.',
    Consts::BREAKDANCE_FS_ERROR_DIRECTORY_DOES_NOT_EXIST => 'Directory "%s" does not exist.',
    Consts::BREAKDANCE_FS_ERROR_NOT_A_DIRECTORY => '"%s" is expected to be a directory, but it\'s not.',
    Consts::BREAKDANCE_FS_ERROR_NOT_A_FILE => '"%s" is expected to be a file, but it\'s not.',
];

/**
 * TODO user should understand what should he do once one of those errors occurred
 * @var array<int, string>
 */
const PHP_FILE_UPLOAD_ERROR_MESSAGES = array(
    UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive value defined at php.ini',
    UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
);
