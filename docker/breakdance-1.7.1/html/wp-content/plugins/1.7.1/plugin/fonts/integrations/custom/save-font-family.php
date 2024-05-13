<?php

namespace Breakdance\CustomFonts;

use Breakdance\Lib\Vendor\Sabberworm\CSS\Parser;
use Breakdance\Lib\Vendor\Sabberworm\CSS\RuleSet\AtRuleSet;

use Breakdance\Filesystem\Consts;

use function Breakdance\Filesystem\clear_bucket_contents;
use function Breakdance\Filesystem\HelperFunctions\generate_error_msg_from_fs_wp_error;
use function Breakdance\Filesystem\HelperFunctions\get_file_url;
use function Breakdance\Filesystem\HelperFunctions\is_fs_error;
use function Breakdance\Filesystem\write_file_to_bucket;
use function Breakdance\Fonts\font;
use function Breakdance\Preferences\get_preferences;
use function Breakdance\Render\appendHashToFilePathAsUrlQueryForCacheBusting;

if ( !defined('FONT_CSS_FILE_FETCH_TIMEOUT') ) {
    define('FONT_CSS_FILE_FETCH_TIMEOUT', 20);	// 20 second timeout
}

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_save_font_families',
        'Breakdance\CustomFonts\save_font_families',
        'edit',
        false,
        [
            'args' => [
                'customFontFamilies' => FILTER_UNSAFE_RAW,
            ]
        ]
    );
    \Breakdance\AJAX\register_handler(
        'breakdance_get_font_families_from_css_file',
        'Breakdance\CustomFonts\get_font_families_from_css_file',
        'edit',
        false,
        [
            'args' => [
                'cssUrl' => FILTER_UNSAFE_RAW
            ]
        ]
    );
});

/**
 * @param string $customFontFamilies
 * @return array|string[]
 */
function save_font_families($customFontFamilies)
{
    $fontFamilies = json_decode($customFontFamilies, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => "error decoding request"];
    }

    $response = [];

    // Delete all .css files since we're going to recreate them.
    // That way we don't leave old unused files
    clear_bucket_contents(Consts::BREAKDANCE_FS_BUCKET_FONT_FAMILIES);

    foreach ($fontFamilies as $fontObj) {
        $fontFamily = (string) $fontObj['family'];
        $fallbackString = $fontObj['fallbackString'];
        $id = $fontObj['id'];

        if ($fontObj['hasExternalCssUrl']) {
            $cssUrl = $fontObj['cssUrl'];

            $response[] = [
                'fontId' => $id,
                'cssUrl' => $cssUrl,
                'hasExternalCssUrl' => true,
                'fontRegistrationData' => font(
                    $id,
                    addQuotesToCssNameIfNecessary($fontObj['cssName']),
                    $fontFamily,
                    $fallbackString,
                    [
                        'styles' => [$cssUrl],
                    ]
                )
            ];

        } else {
            $outputFileBasename = $id . '.css';
            $fontFacesCss = generate_css_for_font_faces($fontFamily, $fontObj['faces']);
            $writeErrorOrFilename = write_file_to_bucket(Consts::BREAKDANCE_FS_BUCKET_FONT_FAMILIES, $outputFileBasename, $fontFacesCss);

            if (!is_fs_error($writeErrorOrFilename)) {
                $fontFilePath = appendHashToFilePathAsUrlQueryForCacheBusting($writeErrorOrFilename, $fontFacesCss);
                $fontUrl = get_file_url(Consts::BREAKDANCE_FS_BUCKET_FONT_FAMILIES, $fontFilePath);

                /** @var \Breakdance\Fonts\ElementDependenciesAndConditions $dependencies */
                $dependencies = [
                    'styles' => [$fontUrl],
                ];

                $fontRegistrationData = font(
                    $id,
                    addQuotesToCssNameIfNecessary($fontFamily),
                    $fontFamily,
                    $fallbackString,
                    $dependencies
                );

                $response[] = [
                    'fontId' => $id,
                    'cssUrl' => $fontUrl,
                    'hasExternalCssUrl' => false,
                    'fontRegistrationData' => $fontRegistrationData,
                ];
            } else {
                return ['error' => generate_error_msg_from_fs_wp_error($writeErrorOrFilename)];
            }
        }
    }

    return $response;
}

/**
 * @param string $cssUrl
 * @return array
 */
function get_font_families_from_css_file($cssUrl) {
    $remoteFontFaces = validate_remote_font_css($cssUrl);
    if (is_wp_error($remoteFontFaces)) {
        $errorMessages = $remoteFontFaces->get_error_messages();
        return ['error' => array_shift($errorMessages)];
    }

    foreach ($remoteFontFaces as $fontFace) {
        $fontId = 'custom_font_' . str_replace(' ', '_', strtolower($fontFace));
        $response[] = [
            'fontId' => $fontId,
            'cssUrl' => $cssUrl,
            'hasExternalCssUrl' => true,
            'fontRegistrationData' => font(
                $fontId,
                addQuotesToCssNameIfNecessary($fontFace),
                ucwords(str_replace(['_', '-'], ' ', $fontFace)),
                '',
                [
                    'styles' => [$cssUrl],
                ]
            )
        ];
    }
    return $response;
}

/**
 * @return array|string[]
 */
function regenerateFontFiles(){
    return save_font_families(json_encode(get_preferences()['customFonts']));
}

/**
 * @param BreakdanceFont $fontObject
 * @return string[]|\WP_Error
 */
function validate_remote_font_css($cssUrl)
{
    $response = wp_safe_remote_get($cssUrl, ['timeout' => FONT_CSS_FILE_FETCH_TIMEOUT]);
    if ( is_wp_error($response) ) {
        return $response;
    }

    $cssDocumentBody = wp_remote_retrieve_body( $response );
    $parsedCssDocument = (new Parser($cssDocumentBody))->parse();
    $fontFacesFoundInRemoteDocument = [];
    foreach ($parsedCssDocument->getAllRulesets() as $ruleset) {
        if ($ruleset instanceof AtRuleSet && $ruleset->atRuleName()  === 'font-face'){
            $rules = $ruleset->getRules('font-family');
            foreach ($rules as $family) {
                $cssName = trim((string) $family->getValue(), '"');
                if(!in_array($cssName, $fontFacesFoundInRemoteDocument, true)) {
                    $fontFacesFoundInRemoteDocument[] = $cssName;
                }
            }
        }
    }

    if (empty($fontFacesFoundInRemoteDocument)) {
        return new \WP_Error(
            'no_font_faces_found',
            "No @font-face declarations found in remote CSS file",
            $fontFacesFoundInRemoteDocument
        );
    }

    return $fontFacesFoundInRemoteDocument;
}

/**
 * @param string $fontFamily
 * @param array $fontFaces
 * @return string
 */
function generate_css_for_font_faces(string $fontFamily, array $fontFaces): string
{
    $fontFacesCss = '';
    foreach ($fontFaces as $font) {
        $srcString = '';

        foreach ($font['files'] as $key => $src) {
            // Separate the urls if there is more than 1
            if ($key > 0) {
                $srcString .= ",\n\t    ";
            }

            $srcString .= "url('{$src['fileUrl']}') format('{$src['format']}')";
        }

        // White space is added to the file so we avoid it here
        $fontFacesCss .= "
@font-face {
  font-family: '$fontFamily';
  font-style: {$font['style']};
  font-weight: {$font['weight']};
  font-display: swap;
  src: $srcString;
}
";
    }

    return $fontFacesCss;
}
