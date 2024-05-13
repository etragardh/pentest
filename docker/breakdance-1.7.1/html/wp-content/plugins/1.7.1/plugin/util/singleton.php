<?php

namespace Breakdance;

trait Singleton
{
    /** @var self|null */
    private static $instance = null;

    /**
     * @return self
     */
    final public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // Prevent cloning of the instance
    public function __clone()
    {
    }

    // Prevent deserialization of the instance
    public function __wakeup()
    {
    }
}
