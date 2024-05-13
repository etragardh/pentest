<?php

namespace Breakdance\DynamicData;

abstract class RepeaterField extends Field
{
    /**
     * @param int | false $postId
     * @return boolean
     */
    abstract public function hasSubFields($postId = null);

    /**
     * @param int $index
     * @return void
     */
    abstract public function setSubFieldIndex($index);

    /**
     * @return RepeaterField|false
     */
    abstract public function parentField();

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Repeaters';
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['repeater'];
    }

    /**
     * @inheritDoc
     */
    abstract public function handler($attributes): RepeaterData;

}
