<?php

namespace Krenor\Prometheus;

class Sample
{
    /**
     * @var float
     */
    protected $value;

    /**
     * @var array
     */
    protected $data;

    /**
     * Sample constructor.
     *
     * @param float $value
     * @param array $data
     */
    public function __construct(float $value, array $data)
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
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }
}
