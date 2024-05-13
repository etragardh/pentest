<?php

namespace Breakdance\Forms\Submission;


use function Breakdance\Forms\getFormSettings;

const DEFAULT_HEADERS = [
        'form' => 'Form',
        'formId' => 'Form ID',
        'post' => 'Post',
        'postId' => 'Post ID',
        'user' => 'User',
        'userIP' => 'User IP',
        'referer' => 'Referer',
        'userAgent' => 'User Agent',
        'submittedOn' => 'Submitted on',
        'updatedOn' => 'Updated on',
    ];

/**
 * @param \WP_Query $formSubmissions
 * @return array<string, string>
 */
function getFieldsForHeaderRow($formSubmissions)
{
    if (!$formSubmissions->have_posts()) {
        return [];
    }

    $mostRecentSubmissionKey = array_key_first($formSubmissions->posts);
    /** @var \WP_Post $mostRecentSubmission */
    $mostRecentSubmission = $formSubmissions->posts[$mostRecentSubmissionKey];
    $mostRecentSubmissionMeta = getMeta($mostRecentSubmission->ID);
    $settings = getFormSettings($mostRecentSubmissionMeta['postId'], $mostRecentSubmissionMeta['formId']);

    if ($settings === null) {
        // This form has likely been deleted,
        // we can try to pull the settings from
        // the most recent submission's metadata
        // or otherwise just return the default headers
        if ($mostRecentSubmissionMeta['settings'] === null) {
            return DEFAULT_HEADERS;
        }
        $settings = $mostRecentSubmissionMeta['settings'];
    }

    $formHeaders = [];
    foreach ($settings['form']['fields'] as $field) {
        $formHeaders[$field['advanced']['id']] =  $field['label'];
    }

    return array_merge($formHeaders, DEFAULT_HEADERS);
}

if (isset($_GET['breakdance_action']) && $_GET['breakdance_action'] === 'export_submissions_to_csv') {
    add_action('admin_init', 'Breakdance\Forms\Submission\handleExport');
}

/**
 * @return void
 */
function handleExport()
{
    if (!current_user_can('edit_posts') || !is_admin()) {
        status_header(401);
        exit('Security check error');
    }

    /** @var string $nonce */
    $nonce = $_GET['_wpnonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'breakdance_export_submissions_to_csv')) {
        status_header(401);
        exit('Security check error');
    }

    $formId = \Breakdance\Forms\Submission\getFormIdFromRequest();
    if (!$formId) {
        exportMultipleFormsAsZip();
        exit();
    }

    exportSingleFormAsCsv($formId['postId'], $formId['formId']);
    exit();
}

/**
 * @param int $postId
 * @param int $formId
 * @return void
 */
function exportSingleFormAsCsv($postId, $formId)
{
    $siteDomain = parse_url(site_url(), PHP_URL_HOST);
    $csvOutputFilename = sprintf('%s-submissions-export-%s-%s.csv', $siteDomain, $formId, (new \DateTime())->format('Y-m-d-G-i'));
    $csvFileHandler = fopen('php://output', 'wb');
    if (!$csvFileHandler) {
        return;
    }
    fprintf($csvFileHandler, chr(0xEF) . chr(0xBB) . chr(0xBF));
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Description: File Transfer');
    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename={$csvOutputFilename}");
    header('Expires: 0');
    header('Pragma: public');

    ob_start();

    outputSubmissionsAsCsv($postId, $formId, $csvFileHandler);

    ob_end_flush();
}

/**
 * @return void
 */
function exportMultipleFormsAsZip()
{
    $formsWithSubmissions = getUniqueFormsFromSubmissions();
    $temporaryFile = wp_tempnam("breakdance-submission-export-zip");

    $siteDomain = parse_url(site_url(), PHP_URL_HOST);
    $currentDate = new \DateTime();
    $csvOutputFilenameFormat =  '%s-submissions-export-%s-%s-%s.csv';
    $zipOutputFilename = $siteDomain . '-submissions-export-' . $currentDate->format('Y-m-d-G-i') . '.zip';

    $zip = new \ZipArchive();
    $zip->open($temporaryFile, \ZipArchive::OVERWRITE);
    foreach ($formsWithSubmissions as $form) {
        $filePath = wp_tempnam("breakdance-submission-export-csv");
        $csvFile = fopen($filePath, 'wb');
        $submissionCount = outputSubmissionsAsCsv($form['postId'], $form['formId'], $csvFile);
        $csvOutputFilename = sprintf($csvOutputFilenameFormat, $siteDomain, $form['postId'], $form['formId'], (new \DateTime())->format('Y-m-d-G-i'));
        fclose($csvFile);
        if ($submissionCount > 0) {
            $zip->addFile($filePath, $csvOutputFilename);
        }
    }
    $zip->close();

    header("Content-type: application/zip");
    header("Content-Disposition: attachment; filename=" . $zipOutputFilename);
    header("Content-length: " . filesize($temporaryFile));
    header("Pragma: no-cache");
    header("Expires: 0");
    readfile($temporaryFile);
}

/**
 * @return bool
 */
function hasActiveFilters()
{
    if (array_key_exists('form_id', $_GET) && $_GET['form_id'] > 0) {
        return true;
    }
    return count(getActiveFilters()) > 0;
}

/**
 * @return array{m?: string, author?: string, post_status?: string}
 */
function getActiveFilters()
{
    $filters = [];

    if (array_key_exists('m', $_GET) && $_GET['m'] !== '0') {
        $filters['m'] = (string) $_GET['m'];
    }

    if (array_key_exists('author', $_GET)) {
        $filters['author'] = (string) $_GET['author'];
    }

    if (array_key_exists('post_status', $_GET) && $_GET['post_status'] !== 'all') {
        $filters['post_status'] = (string) $_GET['post_status'];
    }

    return $filters;
}

/**
 * @global \WP_Post $post
 * @param int $postId
 * @param int $formId
 * @param resource $fileHandler
 * @return int
 */
function outputSubmissionsAsCsv($postId, $formId, $fileHandler)
{
    global $post;

    $submissionsPerLoop = 100;
    $loopOffset = 0;
    $outputSubmissionCount = 0;
    $headers = [];

    // Get any active filters
    $queryArguments = getActiveFilters();

    // add default query arguments
    $queryArguments['post_type'] = 'breakdance_form_res';
    $queryArguments['no_found_rows'] = true;
    $queryArguments['update_post_term_cache'] = false;

    if ($postId && $formId) {
        $queryArguments['meta_query'] = [
            ['key' => '_breakdance_post_id', 'value' => $postId],
            ['key' => '_breakdance_form_id', 'value' => $formId],
        ];
    }

    // This loop will be broken when the
    // query doesn't return any posts
    while (true) {
        $queryArguments['posts_per_page'] = $submissionsPerLoop;
        $queryArguments['offset'] = $loopOffset;

        $formSubmissions = new \WP_Query($queryArguments);

        if (!$formSubmissions->have_posts()) {
            break;
        }

        if (empty($headers)) {
            $headers = getFieldsForHeaderRow($formSubmissions);
            fputcsv($fileHandler, array_values($headers));
        }

        while ($formSubmissions->have_posts()) {
            $formSubmissions->the_post();
            $meta = getMeta($post->ID);
            $settings = $meta['settings'];

            if ($settings === null) {
                // This should only happen on old
                // submissions that were created
                // before stored settings with meta data
                continue;
            }

            $submissionValues = getFieldsForRow($headers, $meta);
            fputcsv($fileHandler, $submissionValues);
            $outputSubmissionCount += 1;

            // Free up memory after each loop
            wp_cache_delete($post->ID, 'posts');
            wp_cache_delete($post->ID, 'post_meta');
            wp_reset_postdata();
        }

        $loopOffset += $submissionsPerLoop;
    }

    return $outputSubmissionCount;
}

/**
 * @param array<string, string> $headers
 * @param FormSubmissionMeta $meta
 * @return array
 */
function getFieldsForRow($headers, $meta)
{
    $submissionValues = [];
    foreach ($headers as $fieldId => $fieldLabel) {
        $fieldValue = '';
        if (array_key_exists($fieldId, $meta['fields'])) {
             if (is_array($meta['fields'][$fieldId])) {
                 $fieldValue = implode(', ', $meta['fields'][$fieldId]);
             } else {
                 $fieldValue = (string) $meta['fields'][$fieldId];
             }
        }
        $submissionValues[$fieldId] = $fieldValue;
    }

    $submissionValues['form'] = $meta['formName'];
    $submissionValues['formId'] = $meta['formId'];
    $submissionValues['post'] = $meta['postTitle'];
    $submissionValues['postId'] = $meta['postId'];
    $submissionValues['user'] = $meta['user'] ? $meta['user']->nickname : '';
    $submissionValues['userIP'] = $meta['ip'];
    $submissionValues['referer'] = $meta['referer'];
    $submissionValues['userAgent'] = $meta['userAgent'];
    $submissionValues['submittedOn'] = $meta['date'];
    $submissionValues['updatedOn'] = $meta['modified'];

    return $submissionValues;
}
