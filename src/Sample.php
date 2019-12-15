<?php

namespace Krenor\Prometheus;

use Tightenco\Collect\Support\Collection;

class Sample
{
    protected string $name;

    protected float $value;

    protected Collection $labels;

    /**
     * Sample constructor.
     *
     * @param string $name
     * @param float $value
     * @param Collection $labels
     */
    public function __construct(string $name, float $value, Collection $labels)
    {
        $this->name = $name;
        $this->value = $value;
        $this->labels = $labels;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function value(): float
    {
        return $this->value;
    }

    /**
     * @return Collection
     */
    public function labels(): Collection
    {
        return $this->labels;
    }
}
