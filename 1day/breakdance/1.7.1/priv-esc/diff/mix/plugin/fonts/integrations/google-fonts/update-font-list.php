#!/usr/bin/env php
<?php
/**
 * This script fetches the latest font list from the Google Font API
 * and stores it in the array{family:string,category:string}[] format
 * in a JSON file
 *
 * command signature: ./update-fontlist.php {Google Font API Key}
 */


if (PHP_SAPI !== 'cli') {
    exit("This script should only be run from the command line");
}

if (count($argv) === 1) {
    _out('You must pass a Google Font API key as the first argument', 'error');
    exit(1);
}
$googleFontApiKey = $argv[1];

require_once __DIR__ . '/constants.php';

if (!defined('\Breakdance\Fonts\Consts::GOOGLE_FONT_FILE')) {
    _out('The GOOGLE_FONT_FILE constant must be defined', 'error');
    exit(1);
}

if (!is_writable(\Breakdance\Fonts\Consts::GOOGLE_FONT_FILE)) {
    _out(\Breakdance\Fonts\Consts::GOOGLE_FONT_FILE . ' is not writable', 'error');
    exit(1);
}

_out('Fetching and validating fonts from Google...', 'info', false);

$fontList = getFontsFromGoogle($googleFontApiKey);
if (array_key_exists('error', $fontList)) {
    _out($fontList['error']['message'], 'error');
    exit(1);
}

$validated_google_font_data = validateApiResponse($fontList);
if (empty($validated_google_font_data)) {
    _out('No validated fonts found in response', 'error');
    exit(1);
}
// "\xE2\x9C\x94" = ✓
_out("\xE2\x9C\x94", 'success');

_out('Writing to file...', 'info', false);
try {
    $fileWritten = file_put_contents(\Breakdance\Fonts\Consts::GOOGLE_FONT_FILE, json_encode($validated_google_font_data, JSON_THROW_ON_ERROR));
    if ($fileWritten === false) {
        _out('Error: Font file was not created at ' . \Breakdance\Fonts\Consts::GOOGLE_FONT_FILE, 'error');
        exit(1);
    }
    // "\xE2\x9C\x94" = ✓
    _out("\xE2\x9C\x94", 'success');
    _out('Google font list updated!', 'success');
    exit(0);
} catch (\JsonException $e) {
    _out($e->getMessage(), 'error');
    exit(1);
}

/**
 * @param $gfonts_api_response_body array{items:array{family:string,category:string}[]}
 * @return array{family:string,category:string}[]
 */
function validateApiResponse($gfonts_api_response_body)
{
    if (!array_key_exists('items', $gfonts_api_response_body)) {
        return [];
    }

    $validated_fonts = [];
    foreach ($gfonts_api_response_body['items'] as $font) {
        if (!array_key_exists('family', $font) || !array_key_exists('category', $font)) {
            continue;
        }
        $validated_fonts[] = [
                'family' => (string)$font['family'],
                'category' => (string)$font['category'],
        ];
    }

    return $validated_fonts;
}

/**
 * Fetch font list from Google API
 *
 * @param string $apiKey
 * @return array{kind:string,items:array{family: string, variants: string[], subsets: string[], version: string, lastModified: string, files: <weight, string[]>, category: string, kind: string}}}
 */
function getFontsFromGoogle($apiKey)
{
    $url = "https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha&key={$apiKey}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);

    if (curl_error($ch)) {
        return ['error' => ["message" => 'Curl Error:' . curl_error($ch)]];
    }

    curl_close($ch);

    try {
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    } catch (\JsonException $e) {
        return ['error' => ["message" => 'Response JSON Error: ' . $e->getMessage()]];
    }
}

/**
 * Output a formatted message to the terminal
 *
 * @param string $message
 * @param string $level
 * @param bool $newLine
 */
function _out($message, $level = 'info', $newLine = true)
{
    $error = "\e[31m";
    $success = "\e[32m";
    $info = "\e[34m";
    $endcolour = "\e[0m";
    $end = $newLine ? " \n" : null;

    echo $$level . $message . $endcolour . $end;
}
