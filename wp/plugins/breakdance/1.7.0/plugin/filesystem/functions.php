<?php

namespace Breakdance\Filesystem;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use WP_Error;

use function Breakdance\Filesystem\HelperFunctions\create_directory;
use function Breakdance\Filesystem\HelperFunctions\delete_file;
use function Breakdance\Filesystem\HelperFunctions\delete_fileinfo_entry;
use function Breakdance\Filesystem\HelperFunctions\generate_error_msg_from_fs_wp_error;
use function Breakdance\Filesystem\HelperFunctions\get_bucket_abs_path;
use function Breakdance\Filesystem\HelperFunctions\get_buckets;
use function Breakdance\Filesystem\HelperFunctions\get_file_url;
use function Breakdance\Filesystem\HelperFunctions\is_fs_error;
use function Breakdance\Filesystem\HelperFunctions\make_fs_wp_error;
use function Breakdance\Filesystem\HelperFunctions\make_path_relative_to_wp_root;
use function Breakdance\Filesystem\HelperFunctions\return_wp_error_if_directory_is_not_writable;
use function Breakdance\Filesystem\HelperFunctions\return_wp_error_if_directory_is_not_writable_or_does_not_exist;
use function Breakdance\Filesystem\HelperFunctions\wp_error_when_it_has_errors_or_true;

/**
 * @return array<string, (string|null)>
 */
function check_all_required_directories(): array
{
    $return = [];
    /** @var array{basedir: string, error: string|false} $wp_upload_dir_result */
    $wp_upload_dir_result = wp_upload_dir();

    $maybe_wp_upload_dir_error_str = $wp_upload_dir_result['error'] ?: null;
    if ($maybe_wp_upload_dir_error_str === null) {
        $maybe_wp_upload_dir_wp_error = return_wp_error_if_directory_is_not_writable_or_does_not_exist(
            $wp_upload_dir_result['basedir']
        );

        $maybe_wp_upload_dir_error_str = is_fs_error($maybe_wp_upload_dir_wp_error)
            ? generate_error_msg_from_fs_wp_error($maybe_wp_upload_dir_wp_error)
            : null;
    }
    $return[make_path_relative_to_wp_root($wp_upload_dir_result['basedir'])] = $maybe_wp_upload_dir_error_str;

    foreach (array_keys(get_buckets()) as $bucket_id) {
        $bucket_dir_abspath = get_bucket_abs_path($bucket_id);
        $maybe_wp_error = return_wp_error_if_directory_is_not_writable_or_does_not_exist($bucket_dir_abspath);
        $return[make_path_relative_to_wp_root($bucket_dir_abspath)] = is_fs_error($maybe_wp_error)
            ? generate_error_msg_from_fs_wp_error($maybe_wp_error)
            : null;
    }

    return $return;
}

/**
 * @return true|WP_Error
 */
function try_to_create_all_required_directories()
{
    $wp_error = new WP_Error();
    /** @var array{error: string|false, basedir: string} $wp_upload_dir_result */
    $wp_upload_dir_result = wp_upload_dir();
    if ($wp_upload_dir_result['error'] === false) {
        foreach (array_keys(get_buckets()) as $bucket_id) {
            $bucket_dir_abspath = get_bucket_abs_path($bucket_id);

            $mkdir_result = create_directory($bucket_dir_abspath);
            if (is_fs_error($mkdir_result)) {
                /** @psalm-suppress UndefinedMethod */
                $wp_error->merge_from($mkdir_result);
            } else {
                $is_writable_err = return_wp_error_if_directory_is_not_writable($bucket_dir_abspath);
                if (is_fs_error($is_writable_err)) {
                    /** @psalm-suppress UndefinedMethod */
                    $wp_error->merge_from($is_writable_err);
                }
            }
        }
    } else {
        /** @psalm-suppress UndefinedMethod */
        $wp_error->merge_from(
            make_fs_wp_error(
                \Breakdance\Filesystem\Consts::BREAKDANCE_FS_ERROR_UNABLE_TO_CREATE_WP_UPLOADS_BASEDIR,
                $wp_upload_dir_result['basedir'],
                $wp_upload_dir_result['error']
            )
        );
    }

    return wp_error_when_it_has_errors_or_true($wp_error);
}

/**
 * Does not remove the bucket directory itself.
 *
 * @param FSBucket $bucket
 * @return true|WP_Error
 */
function clear_bucket_contents(string $bucket)
{
    $bucket_dir_path = get_bucket_abs_path($bucket);

    if (!is_dir($bucket_dir_path)) {
        return make_fs_wp_error(\Breakdance\Filesystem\Consts::BREAKDANCE_FS_ERROR_NOT_A_DIRECTORY, $bucket_dir_path);
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($bucket_dir_path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    $wp_error = new WP_Error();

    /** @var SplFileInfo $fs_item */
    foreach ($iterator as $fs_item) {
        $fs_item_result = delete_fileinfo_entry($fs_item);

        if (is_fs_error($fs_item_result)) {
            /** @psalm-suppress UndefinedMethod */
            $wp_error->merge_from($fs_item_result);
        }
    }

    return wp_error_when_it_has_errors_or_true($wp_error);
}

/**
 * wp_handle_upload performs too much irrelevant actions. Also, it has filters, which may negatively impact expected upload flow.
 *
 * @param FSBucket $bucket
 * @param array{tmp_name: string, error: int} $superglobal_files_element One of the $_FILES assoc array elements
 * @param string $target_file_basename Which filename to use for a moved file. Gets sanitized – file may be saved with a filename that differs from a passed one
 * @return WP_Error|string
 */
function move_uploaded_file_to_bucket(string $bucket, array $superglobal_files_element, string $target_file_basename)
{
    $target_file_basename = sanitize_file_name($target_file_basename);
    $bucket_path_prefix = get_bucket_abs_path($bucket);
    $file_path_with_bucket_path_prefix = path_join($bucket_path_prefix, $target_file_basename);
    $target_directory_path = dirname($file_path_with_bucket_path_prefix);

    if ($superglobal_files_element['error'] !== UPLOAD_ERR_OK) {
        $error_message = PHP_FILE_UPLOAD_ERROR_MESSAGES[$superglobal_files_element['error']] ?? null;

        return make_fs_wp_error(
            \Breakdance\Filesystem\Consts::BREAKDANCE_FS_ERROR_UPLOAD_FAILED,
            $target_file_basename,
            $error_message
        );
    }

    $mkdir_result = create_directory($target_directory_path);
    if (is_fs_error($mkdir_result)) {
        return make_fs_wp_error(
            \Breakdance\Filesystem\Consts::BREAKDANCE_FS_ERROR_UPLOAD_FAILED,
            $target_file_basename,
            $mkdir_result
        );
    }

    // It issues a warning in case of failure which is stupid, error silence operator MUST be here
    $move_result = @move_uploaded_file($superglobal_files_element['tmp_name'], $file_path_with_bucket_path_prefix);

    if ($move_result === false) {
        return make_fs_wp_error(
            \Breakdance\Filesystem\Consts::BREAKDANCE_FS_ERROR_UPLOAD_FAILED,
            $target_file_basename,
            return_wp_error_if_directory_is_not_writable_or_does_not_exist($target_directory_path)
        );
    }

    return $target_file_basename;
}

/**
 * @param FSBucket $bucket
 * @param string $filename Gets sanitized – file may be saved with a filename that differs from a passed one
 * @param string $file_contents
 * @return WP_Error|string
 */
function write_file_to_bucket(string $bucket, string $filename, $file_contents)
{
    $filename = sanitize_file_name($filename);
    $bucket_path_prefix = get_bucket_abs_path($bucket);
    $file_path_with_bucket_path_prefix = path_join($bucket_path_prefix, $filename);
    $directory_path = dirname($file_path_with_bucket_path_prefix);


    $mkdir_result = create_directory($directory_path);
    if (is_fs_error($mkdir_result)) {
        return make_fs_wp_error(
            \Breakdance\Filesystem\Consts::BREAKDANCE_FS_ERROR_FILE_CREATION_FAILED,
            $file_path_with_bucket_path_prefix,
            $mkdir_result
        );
    }

    $directory_access_error = return_wp_error_if_directory_is_not_writable_or_does_not_exist($directory_path);

    $put_result = is_fs_error($directory_access_error) ? false : file_put_contents(
        $file_path_with_bucket_path_prefix,
        $file_contents
    );

    if ($put_result === false) {
        return make_fs_wp_error(
            \Breakdance\Filesystem\Consts::BREAKDANCE_FS_ERROR_FILE_CREATION_FAILED,
            $file_path_with_bucket_path_prefix,
            $directory_access_error
        );
    }

    return $filename;
}


/**
 * @param FSBucket $bucket
 * @param string $filename
 * @return WP_Error|true
 */
function delete_file_at_bucket(string $bucket, string $filename)
{
    $file_path_with_bucket_path_prefix = path_join(get_bucket_abs_path($bucket), $filename);

    return delete_file($file_path_with_bucket_path_prefix);
}
