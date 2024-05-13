<?php

namespace Breakdance\DynamicData;

class LoopController  {

    /** @var self[] */
    public static $instances = [];

    /**
     * @var mixed
     */
    public $field = [];

    /**
     * @param string $id
     * @return self
     */
    public static function getInstance($id = 'default'): self
    {
        if (!array_key_exists($id, self::$instances)) {
            self::$instances[$id] = new self();
        }

        return self::$instances[$id];
    }

    // Prevent cloning of the instance
    public function __clone()
    {
    }

    // Prevent deserialization of the instance
    public function __wakeup()
    {
    }

    /**
     * @param mixed $field
     * @return void
     */
    public function set($field)
    {
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->field;
    }

    public function reset()
    {
        $this->field = [];
    }
}
