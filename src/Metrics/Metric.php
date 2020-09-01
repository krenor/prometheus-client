<?php

namespace Krenor\Prometheus\Metrics;

use Krenor\Prometheus\Contracts\Storage;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Exceptions\LabelException;
use Krenor\Prometheus\Exceptions\PrometheusException;
use Krenor\Prometheus\Contracts\Metric as MetricContract;

abstract class Metric implements MetricContract
{
    protected ?string $namespace = null;

    protected string $name;

    protected string $description;

    protected array $labels = [];

    protected static Storage $storage;

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

        if (!preg_match('/^[a-zA-Z_:][a-zA-Z0-9_:]*$/', $this->key())) {
            throw new PrometheusException("The metric name `{$this->key()}` contains invalid characters.");
        }
    }

    /**
     * @return string
     */
    public function key(): string
    {
        if (!$this->namespace()) {
            return $this->name();
        }

        return "{$this->namespace()}_{$this->name()}";
    }

    /**
     * @return string
     */
    abstract public function type(): string;

    /**
     * @return string|null
     */
    public function namespace(): ?string
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
