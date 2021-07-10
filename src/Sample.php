<?php

namespace Krenor\Prometheus;

use Tightenco\Collect\Support\Collection;

class Sample
{
    /**
     * Sample constructor.
     *
     * @param string $name
     * @param float $value
     * @param Collection $labels
     */
    public function __construct(protected string $name, protected float $value, protected Collection $labels)
    {
        //
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
