<?php

namespace Krenor\Prometheus\Contracts;

use Krenor\Prometheus\CollectorRegistry;

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
     * @var bool
     */
    protected $register = true;

    /**
     * @var CollectorRegistry
     */
    protected $registry;

    // TODO: Multiple Registries: Shoul've

    // TODO: Initialize: Could've

    /**
     * Metric constructor.
     *
     * @param CollectorRegistry $registry
     */
    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;

        if ($this->register) {
            $this->registry->register($this);
        }
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
     * @return string[]
     */
    public function labels(): array
    {
        return $this->labels;
    }

    /**
     * @return bool
     */
    public function registered(): bool
    {
        return $this->registry->get($this) !== null;
    }

    /**
     * @return string
     */
    public function key(): string
    {
        return "{$this->namespace()}:{$this->name()}";
    }
}
