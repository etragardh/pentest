<?php

add_action('init', function() {
    if (defined('DEBUG_WOO_ACTIONS') && DEBUG_WOO_ACTIONS) {
        $actionNames = extract_action_names_from_wc_templates_folder();

        foreach ($actionNames as $actionName) {
            add_action(
                $actionName,
                function($x) use ($actionName) {
                    echo "<div style='padding: 12px; border: 1px solid #ccccff; background-color: #eeeeff;'>";
                    echo $actionName;
                    echo "</div>";
                }
            );
        }

    }
});

function extract_action_names_from_wc_templates_folder() {

    $templatesDirectory = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'woocommerce' . DIRECTORY_SEPARATOR . "templates";

    $fileNames = find_all_files($templatesDirectory);

    $cleanFileNames = array_map(function($fileWithSomePath) {
        $parts = explode('woocommerce' . DIRECTORY_SEPARATOR . 'templates', $fileWithSomePath);
        $cleanName = $parts[1];
        return $cleanName;
    }, $fileNames);

    $allMatchingLines = [];

    foreach ($cleanFileNames as $fileName) {
        $matchingLinesInThisFile = find_matching_lines('do_action', $templatesDirectory . DIRECTORY_SEPARATOR . $fileName);
        $allMatchingLines = array_merge($matchingLinesInThisFile, $allMatchingLines);
    }

    $actionNamesOrFalse = array_map(
        function($matchingLine) {
            if (strpos($matchingLine, "do_action( '") !== false) {
                $parts = explode("do_action( '", $matchingLine);
                $parts2 = explode("'", $parts[1]);
                return $parts2[0];
            } else {
                return false;
            }
        },
        $allMatchingLines
    );

    $actionNames = array_filter($actionNamesOrFalse, function($x) { return !!$x; });

    $actionNamesWithoutEmailActionNames = array_filter(
        $actionNames,
        function ($actionName) {
            return strpos($actionName, "email") === false;
        }
    );

    return $actionNamesWithoutEmailActionNames;

}

// https://www.php.net/manual/en/function.scandir.php#107117
function find_all_files($dir)
{
    $root = scandir($dir);
    foreach($root as $value)
    {
        if($value === '.' || $value === '..') {continue;}
        if(is_file("$dir/$value")) {$result[]="$dir/$value";continue;}
        foreach(find_all_files("$dir/$value") as $value)
        {
            $result[]=$value;
        }
    }
    return $result;
}

// https://stackoverflow.com/a/3686287

function find_matching_lines($searchString, $filePath) {
    $matches = array();

    $handle = @fopen($filePath, "r");
    if ($handle) {
        while (!feof($handle)) {
            $buffer = fgets($handle);
            if(strpos($buffer, $searchString) !== FALSE)
                $matches[] = $buffer;
        }
        fclose($handle);
    }

    return $matches;
}
