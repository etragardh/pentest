<?php

namespace Breakdance\ErrorReporter;

use Breakdance\AJAX\BreakdanceAjaxHandlerException;
use Breakdance\Lib\Vendor\Whoops\Util\Misc;
use Breakdance\Lib\Vendor\Whoops\Handler\Handler;
use Breakdance\Lib\Vendor\Whoops\Exception\Formatter;

use function Breakdance\Admin\is_breakdance_development_environment;

class AjaxHandler extends Handler
{
    /**
     * @var integer
     */
    private $lineBufferSize = 5;

    public function handle()
    {
        // Output buffer is already created for handler purposes by Whoops, we don't need it yet
        if (ob_get_level()) {
            ob_end_clean();
        }

        $is_dev_env = is_breakdance_development_environment();
        $exception = $this->getException();

        // Don't corrupt output JSON when PHP warning happened after echoing JSON
        if (
            !$is_dev_env
            && $exception instanceof \Breakdance\Lib\Vendor\Whoops\Exception\ErrorException
            && $exception->getSeverity() === E_WARNING
        ) {
            $previous_buffer_len = ob_get_length();
            $previous_buffer_contents = ob_get_contents();

            // Output buffer contained non-empty valid JSON before error happened
            if ($previous_buffer_len > 0 && null !== json_decode($previous_buffer_contents)) {
                ob_start();

                return Handler::DONE;
            }
        }

        ob_start();

        if (Misc::canSendHeaders()) {
            $http_status_code = 500;
            $http_status_description = '';

            if ($this->getException() instanceof BreakdanceAjaxHandlerException) {
                /** @var BreakdanceAjaxHandlerException $breakdanceAjaxHandlerException */
                $breakdanceAjaxHandlerException = $this->getException();
                $http_status_code = (int) $breakdanceAjaxHandlerException->getCode();
                $http_status_description = (string) $breakdanceAjaxHandlerException->getStatusDescription();
            }

            status_header($http_status_code, $http_status_description);

            header(
                sprintf(
                    "Content-Type: %s; charset=%s",
                    'application/json',
                    (string) get_option('blog_charset')
                )
            );

            $error = $this->getErrorResponseArray();
            $response = wp_json_encode(compact('error'), JSON_PRETTY_PRINT);

            echo $response ? $response : json_encode((object) []);
        }

        return Handler::QUIT;
    }

    /**
     * @return array
     */
    private function getErrorResponseArray()
    {
        $inspector = $this->getInspector();
        $response = Formatter::formatExceptionAsDataArray(
            $inspector,
            true
        );

        if (isset($response['trace']) && is_array($response['trace'])) {
            /** @var array $trace */
            foreach ($response['trace'] as $index => $trace) {
                $traceWithSnippet = array_merge(
                    $trace,
                    [
                        'snippet' => $this->getCodeSnippet(
                            (string)($trace['file'] ?? null),
                            (int)($trace['line'] ?? null)
                        ),
                        'startingLine' => $this->getStartingLine((int)($trace['line'] ?? null)),
                    ]
                );

                /**
                 * @psalm-suppress MixedArrayAssignment
                 * @psalm-suppress MixedArrayOffset
                 */
                $response['trace'][$index] = $traceWithSnippet;
            }
        }

        return $response;
    }

    /**
     * @param string $file
     * @param integer $errorLine
     * @return array<int, string>|false
     */
    private function getCodeSnippet($file, $errorLine)
    {
        if ($file == '[internal]' || !file_exists($file) || !is_readable($file)) {
            return false;
        }
        $handle = fopen($file, "r");
        if (!$handle) {
            return false;
        }

        $snippet = [];
        $currentLine = 1;
        $lineBufferSize = $this->lineBufferSize;
        $isBeforeLineIndex = $errorLine - $lineBufferSize;
        $isAfterLineIndex = $errorLine + $lineBufferSize;
        $selectableSnippetRange = range($isBeforeLineIndex, $isAfterLineIndex - 1);
        while (($line = fgets($handle)) !== false) {
            if (in_array($currentLine, $selectableSnippetRange)) {
                $snippet[$currentLine] = $line;
            }

            $currentLine++;
        }

        fclose($handle);

        return $snippet;
    }

    /**
     * @param integer $line
     * @return integer
     */
    private function getStartingLine(int $line)
    {
        $startingLine = $line - $this->lineBufferSize;

        return $startingLine > 0 ? $startingLine : 1;
    }
}
