<?php

namespace Breakdance\Data;

class RecursiveTreeNodeIterator extends \RecursiveArrayIterator
{
    public function hasChildren(): bool
    {
        /** @var TreeNode|mixed $maybeNode */
        $maybeNode = $this->current();

        return is_array($maybeNode) && isset($maybeNode['children'], $maybeNode['id']) && is_array($maybeNode['children']);
    }

    public function getChildren(): ?\RecursiveArrayIterator
    {
        /** @var TreeNode $current */
        $current = $this->current();
        return new self($current['children']);
    }
}
