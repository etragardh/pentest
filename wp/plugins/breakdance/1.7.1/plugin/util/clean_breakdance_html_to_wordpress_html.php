<?php

namespace Breakdance\Util;

use Breakdance\Lib\Vendor\League\HTMLToMarkdown\HtmlConverter;
use Breakdance\Lib\Vendor\cebe\markdown\MarkdownExtra;

/**
 * Convert the raw HTML to markdown and then bring it back as a simplified content version of HTML.
 *
 * @param string $html
 * @return string
 */
function clean_breakdance_html_to_wordpress_html($html)
{

    $html_to_markdown_converter = new HtmlConverter();
    //strip HTML tags that don't have a Markdown equivalent while preserving the content inside them
    $html_to_markdown_converter->getConfig()->setOption('strip_tags', true);
    //strip  placeholder links
    $html_to_markdown_converter->getConfig()->setOption('strip_placeholder_links', true);
    //use the full link syntax
    $html_to_markdown_converter->getConfig()->setOption('use_autolinks', true);

    $markdown_content = $html_to_markdown_converter($html);

    $markdown_to_html_converter = new MarkdownExtra();
    $markdown_to_html_converter->html5 = true;

    $cleaned_content = $markdown_to_html_converter->parse($markdown_content);

    return $cleaned_content;
}
