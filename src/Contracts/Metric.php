<?php

namespace Krenor\Prometheus\Contracts;

abstract class Metric
{
    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string[]
     */
    protected $labels = []; // TODO: What about injecting the values, if any, through the constructor?

    /**
     * @var Storage
     */
    protected static $storage;

    /**
     * @return string
     */
    abstract public function type(): string;

    /**
     * @return string
     */
    public function namespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return string[]
     */
    public function labels(): array
    {
        return $this->labels;
    }

    /**
     * @param Storage $storage
     */
    public static function storeUsing(Storage $storage)
    {
        static::$storage = $storage;
    }

    /**
     * @return Storage
     */
    public static function storage()
    {
        return static::$storage;
    }
}
