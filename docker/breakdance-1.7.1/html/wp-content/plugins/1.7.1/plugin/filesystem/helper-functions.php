<?php

namespace Breakdance\Filesystem\HelperFunctions;

use Exception;
use SplFileInfo;
use Breakdance\Filesystem\Consts;
use WP_Error;

use const Breakdance\Filesystem\BREAKDANCE_FS_BUCKETS;
use const Breakdance\Filesystem\BREAKDANCE_FS_OPERATION_ERROR_MESSAGES;
use const Breakdance\Filesystem\BREAKDANCE_UPLOADS_SUBDIR_REL_PATH;

/**
 * @param string $fs_error_code
 * @param string $fs_object_path
 * @param WP_Error|string|null $error_which_caused_this_error
 * @return WP_Error
 */
function make_fs_wp_error(string $fs_error_code, string $fs_object_path, $error_which_caused_this_error = null)
{
    return new WP_Error($fs_error_code, $fs_object_path, $error_which_caused_this_error);
}

function make_path_relative_to_wp_root(string $path): string
{
    /** @psalm-suppress UndefinedConstant **/
    return str_replace(
        trailingslashit((string) ABSPATH),
        '',
        $path
    );
}

function generate_error_msg_from_fs_wp_error(WP_Error $fs_wp_error): string
{
    $error_message = '';
    /** @psalm-var FSError[] $error_codes */
    $error_codes = $fs_wp_error->get_error_codes();
    foreach ($error_codes as $error_code) {
        if (isset(BREAKDANCE_FS_OPERATION_ERROR_MESSAGES[$error_code])) {
            // Although we operate with absolute paths, it's insecure to expose absolute paths
            $file_path = make_path_relative_to_wp_root($fs_wp_error->get_error_message($error_code));

            $error_message .=
                (strlen($error_message) === 0 ? '' : PHP_EOL)
                . sprintf(
                    BREAKDANCE_FS_OPERATION_ERROR_MESSAGES[$error_code],
                    $file_path
                );

            /** @var WP_Error|string $reason */
            /** @psalm-suppress MixedAssignment */
            if ($reason = $fs_wp_error->get_error_data($error_code)) {
                $error_message .= ' Reason: ';
                if (is_fs_error($reason)) {
                    $error_message .= generate_error_msg_from_fs_wp_error($reason);
                } else {
                    $error_message .= (string) $reason;
                }
            }
        } else {
            $error_message .= (string) $error_code;
        }
    }
    return $error_message;
}

/**
 * @param WP_Error $wp_error
 * @return true|WP_Error
 */
function wp_error_when_it_has_errors_or_true(WP_Error $wp_error)
{
    return $wp_error->has_errors() ? $wp_error : true;
}

/**
 * @param string $dir_abs_path
 * @return WP_Error|null
 */
function return_wp_error_if_directory_is_not_writable(string $dir_abs_path)
{
    if (!is_writeable($dir_abs_path)) {
        return make_fs_wp_error(Consts::BREAKDANCE_FS_ERROR_DIRECTORY_NON_WRITABLE, $dir_abs_path);
    }
    return null;
}

/**
 * @param string $dir_abs_path
 * @return WP_Error|null
 */
function return_wp_error_if_directory_does_not_exist(string $dir_abs_path)
{
    if (!is_dir($dir_abs_path)) {
        return make_fs_wp_error(Consts::BREAKDANCE_FS_ERROR_DIRECTORY_DOES_NOT_EXIST, $dir_abs_path);
    }
    return null;
}

/**
 * @param string $dir_abs_path
 * @return WP_Error|null
 */
function return_wp_error_if_directory_is_not_writable_or_does_not_exist(string $dir_abs_path)
{
    if (is_fs_error(($is_dir = return_wp_error_if_directory_does_not_exist($dir_abs_path)))) {
        return $is_dir;
    }

    if (is_fs_error(($is_writable = return_wp_error_if_directory_is_not_writable($dir_abs_path)))) {
        return $is_writable;
    }

    return null;
}

function join_url_path_parts(string $left, string $right): string
{
    return rtrim($left, '/') . '/' . ltrim($right, '/');
}

function get_uploads_dir_abs_path(): string
{
    // wp_get_upload_dir() is lightweight and does not attempt to create uploads dir
    /** @var array{basedir: string} $wp_upload_dir */
    $wp_upload_dir = wp_get_upload_dir();
    $wp_uploads_dir = $wp_upload_dir['basedir'];
    $breakdance_uploads_dir = path_join($wp_uploads_dir, wp_normalize_path(BREAKDANCE_UPLOADS_SUBDIR_REL_PATH));

    return $breakdance_uploads_dir;
}

function get_uploads_url(): string
{
    // wp_get_upload_dir() is lightweight and does not attempt to create uploads dir
    /** @var array{baseurl: string} $wp_upload_dir */
    $wp_upload_dir = wp_get_upload_dir();

    $wp_uploads_url = $wp_upload_dir['baseurl'];

    $breakdance_uploads_url = path_join($wp_uploads_url, wp_normalize_path(BREAKDANCE_UPLOADS_SUBDIR_REL_PATH));

    return set_url_scheme($breakdance_uploads_url);
}

/**
 * @psalm-param FSBucket $bucket
 * @return string
 * @throws Exception
 */
function validate_bucket_exists_and_return_its_path(string $bucket)
{
    $buckets = get_buckets();

    if (!array_key_exists($bucket, $buckets)) {
        throw new Exception(sprintf('Filesystem bucket "%s" does not exist.', $bucket));
    }

    return $buckets[$bucket];
}

/**
 * @psalm-param FSBucket $bucket
 * @return string
 * @throws Exception
 */
function get_bucket_abs_path(string $bucket): string
{
    $bucket_uploads_subpath_or_abs_path = wp_normalize_path(validate_bucket_exists_and_return_its_path($bucket));

    if (is_absolute_path($bucket_uploads_subpath_or_abs_path)) {
        return $bucket_uploads_subpath_or_abs_path;
    } else {
        $breakdance_uploads_dir = get_uploads_dir_abs_path();

        return path_join(
            $breakdance_uploads_dir,
            $bucket_uploads_subpath_or_abs_path
        );
    }
}

/**
 * @psalm-param FSBucket $bucket
 * @return string
 * @throws Exception
 */
function get_bucket_url(string $bucket): string
{
    /** @var string $bucket_uploads_subpath_or_abs_path */
    $bucket_uploads_subpath_or_abs_path = validate_bucket_exists_and_return_its_path($bucket);

    if (is_absolute_path($bucket_uploads_subpath_or_abs_path)) {
        throw new Exception(sprintf('Unable to determine an URL for absolute-path defined bucket "%s".', $bucket));
    }

    $breakdance_uploads_url = get_uploads_url();

    return join_url_path_parts($breakdance_uploads_url, $bucket_uploads_subpath_or_abs_path);
}

/**
 * @param string $path
 * @return bool
 */
function is_absolute_path($path)
{
    if (strlen($path) === 0 || '.' === $path[0]) {
        return false;
    }

    if (preg_match('/^[a-zA-Z]:[\/\\\\]/', $path)) {
        return true;
    }

    return in_array($path[0], ['/', '\\'], true);
}

/**
 * @psalm-param FSBucket $bucket
 * @param string $file_rel_path
 * @return string
 * @throws Exception
 */
function get_file_url(string $bucket, string $file_rel_path): string
{
    return join_url_path_parts(get_bucket_url($bucket), $file_rel_path);
}

/**
 * @param SplFileInfo $fs_item
 * @return true|WP_Error
 */
function delete_fileinfo_entry(SplFileInfo $fs_item)
{
    try {
        // SplFileInfo instances are created using this value,
        // so it always returns a path, even when file doesn't exist anymore:
        $path = $fs_item->getPathname();

        /** @var WP_Error|true $result */
        $result = true;

        /**
         * This may throw an exception
         * @link https://www.php.net/manual/en/splfileinfo.gettype.php
         */
        $fs_item_type = $fs_item->getType();

        switch ($fs_item_type) {
            case false:
                $result = false;
                break;
            case 'dir':
                $path = $fs_item->getRealPath();
                $result = @rmdir($path);
                break;
            case 'file':
            default:
                $path = $fs_item->getRealPath();
                if ($path === false) {
                    // getRealPath() returns false if the file does not exist, which I believe should be treated as successful removal
                    $result = true;
                } else {
                    $result = delete_file($path);
                }
                break;
            case 'link':
                $path = $fs_item->getPathname();
                $result = delete_file($path);
                break;
        }

        if ($result === true) {
            return true;
        } elseif (is_fs_error($result)) {
            return $result;
        } else {
            return make_fs_wp_error(
                Consts::BREAKDANCE_FS_ERROR_REMOVAL_FAILED,
                $path
            );
        }
    } catch (\Exception $e) {
        return make_fs_wp_error(
            Consts::BREAKDANCE_FS_ERROR_REMOVAL_FAILED,
            $path ?? '',
            (string) $e
        );
    }
}

/**
 * @param string $file_abs_path
 * @return true|WP_Error
 */
function delete_file(string $file_abs_path)
{
    if (is_dir($file_abs_path)) {
        // a: Unlink won't work
        // b: This function is for files only
        return make_fs_wp_error(
            Consts::BREAKDANCE_FS_ERROR_REMOVAL_FAILED,
            $file_abs_path,
            make_fs_wp_error(Consts::BREAKDANCE_FS_ERROR_NOT_A_FILE, $file_abs_path)
        );
    }

    if (!file_exists($file_abs_path)) {
        // An attempt to delete non-existent file should not result in a failed operation, because the goal is achieved
        return true;
    }

    // It issues a warning in case of failure which is stupid, error silence operator MUST be here
    $unlink_result = @unlink($file_abs_path);

    if ($unlink_result) {
        return true;
    } else {
        // Let's try to find out a reason why it's failed
        $file_dir_abs_path = dirname($file_abs_path);

        return make_fs_wp_error(
            Consts::BREAKDANCE_FS_ERROR_REMOVAL_FAILED,
            $file_abs_path,
            // Checking it in advance may cause problems, because is_writable() sometimes returns false-negative result
            return_wp_error_if_directory_is_not_writable_or_does_not_exist($file_dir_abs_path)
        );
    }
}

/**
 * @param string $dir_abs_path
 * @return true|WP_Error
 */
function create_directory(string $dir_abs_path)
{
    if (!wp_mkdir_p($dir_abs_path)) {
        // mkdir failed, let's try to guess why
        $parent_dir_abspath = dirname($dir_abs_path);

        // We need to find the parent folder that exists
        while (
            '.' !== $parent_dir_abspath
            && !is_dir($parent_dir_abspath)
            && dirname($parent_dir_abspath) !== $parent_dir_abspath
        ) {
            $parent_dir_abspath = dirname($parent_dir_abspath);
        }

        return make_fs_wp_error(
            Consts::BREAKDANCE_FS_ERROR_MKDIR_FAILED,
            $dir_abs_path,
            return_wp_error_if_directory_is_not_writable_or_does_not_exist($parent_dir_abspath)
        );
    } else {
        clearstatcache(true, $dir_abs_path);
    }

    return true;
}


function show_unavailable_directories_admin_notice()
{
    $class = 'notice notice-error';
    $message = sprintf(
        'One or more directories required for <b>Breakdance</b> to function' .
        ' properly are missing or aren\'t writable. <a href="%s" target="_blank">Click here</a> for more details.',
        admin_url('admin.php?page=breakdance_settings&tab=tools#create_directories_row')
    );

    printf('<div class="%s"><p>%s</p></div>', $class, $message);
}

/**
 * Makes Psalm happy by using type assertion.
 *
 * @psalm-assert-if-true WP_Error $thing
 *
 * @param WP_Error|mixed $thing
 * @return bool
 */
function is_fs_error($thing): bool
{
    return is_wp_error($thing);
}

/**
 * Makes Psalm happy because it doesn't allow to specify a type for a constant defined using define() function.
 * @psalm-return array<FSBucket, string>
 * @psalm-suppress MixedInferredReturnType
 */
function get_buckets() {
    /** @psalm-suppress MixedReturnStatement */
    return BREAKDANCE_FS_BUCKETS;
}
