<?php

namespace Breakdance\Forms;

/**
 * Move all files to the appropriate upload directory.
 * @param int $formId
 * @param NormalizedUploadedFiles $files
 * @param FormSettings $settings
 * @return FormFileGroup
 */
function handleUploadedFiles($formId, $files, $settings)
{
    $overrides = ['test_form' => false];

    /** @var FormFileGroup $output */
    $output = [];

    if (empty($files)) return [];

    add_filter('upload_dir',
        /**
         * @param WordPressUploadDirectory $uploads
         * @return array
         */ static function ($uploads) use ($formId) {
            return setBreakdanceUploadDirectory($uploads, $formId);
        });

    foreach ($settings['form']['fields'] as $field) {
        if ($field['type'] !== 'file') {
            continue;
        }

        $allowedFileTypes = $field['allowed_file_types'] ?? [];
        if (!empty($field['allowed_file_types'])) {
            $allowedMimeTypes = array_filter(get_allowed_mime_types(), static function ($mimeType) use ($allowedFileTypes) {
                return in_array($mimeType, $allowedFileTypes);
            });
            if (!empty($allowedMimeTypes)) {
                $overrides['mimes'] = $allowedMimeTypes;
            }
        }

        $fieldId = getIdFromField($field);
        if (array_key_exists($fieldId, $files)) {
            $fileField = $files[$fieldId];
            foreach ($fileField as $file) {
                /** @var FormFile $uploaded */
                $uploaded = wp_handle_upload($file, $overrides);
                $uploaded['fieldId'] = $fieldId;

                if ($uploaded && !isset($uploaded['error'])) {
                    $output[$fieldId][] = $uploaded;
                }
            }
        }
    }

    return $output;
}

/**
 * @param WordPressUploadDirectory $uploads
 * @param int $formId
 * @return WordPressUploadDirectory
 */
function setBreakdanceUploadDirectory($uploads, $formId)
{
    $submissionDirectory = getSubmissionDirectory($formId);

    $uploads['path'] = trailingslashit($uploads['basedir']) . $submissionDirectory . $uploads['subdir'];
    $uploads['url'] = trailingslashit($uploads['baseurl']) . $submissionDirectory . $uploads['subdir'];

    return $uploads;
}

/**
 * @param int $formId
 * @return string
 */
function getSubmissionDirectory($formId)
{
    $installUuid = (string) \Breakdance\Data\get_global_option('uuid');

    return 'breakdance/submissions/' . $formId . '-' . wp_hash((string) $formId . ':' . $installUuid);
}

/**
 * Remove an array of files from the server
 * @param FormFileGroup $fields
 */
function cleanUpFiles($fields)
{
    foreach ($fields as $files) {
        foreach ($files as $file) {
            wp_delete_file($file['file']);
        }
    }
}

/**
 * Normalize $_FILES array into a cleaner array and groups by input.
 * @param _FILES $_files
 * @return NormalizedUploadedFiles
 */
function getFilesNormalized($_files)
{
    $files = [];

    foreach ($_files as $key => $field) {
        foreach ($field as $name => $values) {
            foreach ($values as $index => $value) {
                $files[$name][$index][$key] = $value;
            }
        }
    }

    return $files;
}


/**
 * @param int $postId
 * @param int $formId
 * @param string $fieldId
 * @param string $fileUrl
 * @param boolean $forceDownload
 * @return string
 */
function getSecureFileUrl($postId, $formId, $fieldId, $fileUrl, $forceDownload = true)
{
    $downloadUrl = $fileUrl;

    /** @var WordPressUploadDirectory $uploadDirectory */
    $uploadDirectory = wp_upload_dir();
    $submissionDirectoryUrl = trailingslashit($uploadDirectory['baseurl']) . getSubmissionDirectory($formId);
    if (strpos($fileUrl, $submissionDirectoryUrl) !== false) {
        $filePathWithoutSubmissionDirectory = str_replace($submissionDirectoryUrl, '', $fileUrl);
        $downloadUrl = site_url('index.php');
        $hash = generateDownloadHash($postId, $formId, $fieldId, $filePathWithoutSubmissionDirectory);
        $args = [
            'breakdance_download' => urlencode($filePathWithoutSubmissionDirectory),
            'postId' => $postId,
            'formId' => $formId,
            'fieldId' => $fieldId,
            'hash' => $hash,
        ];

        if ($forceDownload) {
            $args['dl'] = 1;
        }

        $downloadUrl = add_query_arg($args, $downloadUrl);
    }

    return $downloadUrl;
}

/**
 * @param int $postId
 * @param int $formId
 * @param string $fieldId
 * @param string $file
 * @return string
 */
function generateDownloadHash($postId, $formId, $fieldId, $file)
{
    $key = absint($postId) . ':' . absint($formId) . ':' . $fieldId . ':' . urlencode($file);

    return hash_hmac('sha256', $key, 'breakdance_download' . wp_salt());
}

add_action('breakdance_loaded', function () {
    add_action('init', function () {
        if (isset($_GET['breakdance_download'])) {
            $file = (string)filter_input(INPUT_GET, 'breakdance_download', FILTER_UNSAFE_RAW);
            $postId = (int)filter_input(INPUT_GET, 'postId', FILTER_VALIDATE_INT);
            $formId = (int)filter_input(INPUT_GET, 'formId', FILTER_VALIDATE_INT);
            $fieldId = (string)filter_input(INPUT_GET, 'fieldId', FILTER_UNSAFE_RAW);
            $hash = (string)filter_input(INPUT_GET, 'hash', FILTER_UNSAFE_RAW);

            $hasPermission = validateDownload($file, $postId, $formId, $fieldId, $hash);
            if ($hasPermission) {
                return deliverFile($formId, $file);
            } else {
                status_header(401, 'Unauthorized');
                exit();
            }
        }
    });
});

/**
 * @param string $file
 * @param int $postId
 * @param int $formId
 * @param string $fieldId
 * @param string $hash
 * @return bool
 */
function validateDownload($file, $postId, $formId, $fieldId, $hash)
{
    $hashCheck = generateDownloadHash($postId, $formId, $fieldId, $file);
    if (!hash_equals($hash, $hashCheck)) {
        return false;
    }

    /** @var WordPressUploadDirectory $uploadDir */
    $uploadDir = wp_upload_dir();

    // validate file path is within the submission directory
    $submissionUploadDirectory = trailingslashit(trailingslashit($uploadDir['basedir']) . getSubmissionDirectory($formId));
    $downloadFilePath = $submissionUploadDirectory . $file;
    $realDownloadFilePath = realpath($downloadFilePath);
    if (!$realDownloadFilePath || strpos($realDownloadFilePath, $submissionUploadDirectory) === false) {
        return false;
    }

    $formSettings = getFormSettings($postId, $formId);

    $restrictFileAccess = $formSettings['actions']['store_submission']['restrict_file_access'] ?? false;
    if ($restrictFileAccess) {
        if (!\Breakdance\Permissions\hasPermission('full')) {
            return false;
        }
    }

    return true;
}

/**
 * @param int $formId
 * @param string $file
 * @return void
 */
function deliverFile($formId, $file)
{
    global $wp_query;

    /** @var \WP_Query $wp_query */
    $wp_query = $wp_query;

    /** @var WordPressUploadDirectory $uploadDir */
    $uploadDir = wp_upload_dir();
    $filePath = trailingslashit($uploadDir['basedir']) . getSubmissionDirectory($formId) . $file;

    if (!is_readable($filePath)) {
        $wp_query->set_404();
        status_header(404);
        $template_path = get_404_template();
        if (file_exists($template_path)) {
            require_once($template_path);
        }
        exit();
    }

    /** @var array{ext: string, type: string} $fileType */
    $fileType = wp_check_filetype($filePath);
    $contentType = $fileType['type'];
    $forceDownload = (bool)filter_input(INPUT_GET, 'dl', FILTER_VALIDATE_BOOLEAN);
    $contentDisposition = $forceDownload ? 'attachment' : 'inline';

    nocache_headers();
    header('X-Robots-Tag: noindex', true);
    header('Content-Type: ' . (string)$contentType);
    header('Content-Description: File Transfer');
    header('Content-Disposition: ' . $contentDisposition . '; filename="' . wp_basename($file) . '"');
    header('Content-Transfer-Encoding: binary');
    // Clear buffer AND turn off output buffering before starting delivery of files requested for download to prevent third-parties to corrupt the file content.
    if (ob_get_contents()) {
        ob_end_clean();
    }
    readfileChunked($filePath);
    exit();
}

/**
 * Reads file in chunks so big downloads are possible without changing PHP.INI
 * See https://github.com/bcit-ci/CodeIgniter/wiki/Download-helper-for-large-files
 *
 * @param string $file
 * @param bool $retbytes
 * @return bool|int
 */
function readfileChunked($file, $retbytes = true)
{

    $chunksize = 1024 * 1024;
    $buffer = '';
    $cnt = 0;
    $handle = @fopen($file, 'r');

    if ($size = @filesize($file)) {
        header('Content-Length: ' . $size);
    }

    if (false === $handle) {
        return false;
    }

    while (!@feof($handle)) {
        $buffer = @fread($handle, $chunksize);
        echo $buffer;

        if ($retbytes) {
            $cnt += strlen($buffer);
        }
    }

    $status = @fclose($handle);

    if ($retbytes && $status) {
        return $cnt;
    }

    return $status;
}
