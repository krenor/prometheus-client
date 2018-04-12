<?php

namespace Krenor\Prometheus;

use Tightenco\Collect\Support\Collection;

class Sample
{
    /**
     * @var float
     */
    protected $value;

    /**
     * @var Collection
     */
    protected $data;

    /**
     * Sample constructor.
     *
     * @param float $value
     * @param Collection $data
     */
    public function __construct(float $value, Collection $data)
    {
        $this->value = $value;
        $this->data = $data;
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
    public function data(): Collection
    {
        return $this->data;
    }
}
