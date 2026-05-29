<?php

namespace Box\Spout\Common;

/**
 * Class Singleton
 * Defines a class as a singleton.
 *
 * @package Box\Spout\Common
 */
/** @phpstan-consistent-constructor */
trait Singleton
{
    protected static $instance;

    /**
     * @return static
     */
    final public static function getInstance()
    {
        return isset(static::$instance)
            ? static::$instance
            : static::$instance = new static;
    }

    /**
     * Singleton constructor.
     */
    private function __construct()
    {
        $this->init();
    }

    /**
     * Initializes the singleton
     * @return void
     */
    protected function init() {}

    public function __wakeup() {}
    public function __clone() {}
}
