<?php

namespace Breakdance\Themeless;

class SearchContext
{

    use \Breakdance\Singleton;

    /** @var string|false */
    public $search = false;

    /**
     * @param string $search
     * @param Closure():mixed $closure
     * @return mixed
     */
    public function executeInContext($search, $closure)
    {
        $this->search = $search;
        /** @psalm-suppress MixedAssignment */
        $result = $closure();
        $this->search = false;

        return $result;
    }

}
