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
    protected $labels = [];

    /**
     * Type constructor.
     *
     * @param string $namespace
     * @param string $name
     * @param string $description
     */
    public function __construct(string $namespace, string $name, string $description)
    {
        $this->namespace = $namespace;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function namespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     *
     * @return Metric
     */
    public function setNamespace(string $namespace): Metric
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Metric
     */
    public function setName(string $name): Metric
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Metric
     */
    public function setDescription(string $description): Metric
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string[]
     */
    public function labels(): array
    {
        return $this->labels;
    }

    /**
     * @param string[] $labels
     *
     * @return Metric
     */
    public function setLabels(array $labels): Metric
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @return string
     */
    public function key(): string
    {
        return "{$this->namespace()}:{$this->name()}";
    }
}
