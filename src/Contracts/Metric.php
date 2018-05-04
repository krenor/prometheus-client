<?php

namespace Krenor\Prometheus\Contracts;

use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Exceptions\LabelException;

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
    protected $labels = [];

    /**
     * @var Storage
     */
    protected static $storage;

    /**
     * @return string
     */
    abstract public function type(): string;

    /**
     * Metric constructor.
     *
     * @throws LabelException
     */
    public function __construct()
    {
        foreach ($this->labels as $label) {
            if (!preg_match('/^(?![_]{2})[a-zA-Z_][a-zA-Z0-9_]*$/', $label)) {
                throw new LabelException("The label `{$label}` contains invalid characters.");
            }
        }
    }

    /**
     * @return string
     */
    final public function key(): string
    {
        return "{$this->namespace()}_{$this->name()}";
    }

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
     * @return Collection
     */
    public function labels(): Collection
    {
        return new Collection($this->labels);
    }

    /**
     * @param Storage $storage
     */
    public static function storeUsing(Storage $storage): void
    {
        static::$storage = $storage;
    }

    /**
     * @return Storage
     */
    public static function storage(): Storage
    {
        return static::$storage;
    }
}
