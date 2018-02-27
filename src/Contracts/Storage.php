<?php

namespace Krenor\Prometheus\Contracts;

use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Contracts\Types\Observable;
use Krenor\Prometheus\Contracts\Types\Decrementable;
use Krenor\Prometheus\Contracts\Types\Incrementable;

interface Storage
{

    public function increment(Incrementable $metric, float $value);

    public function decrement(Decrementable $metric, float $value);

    public function observe(Observable $metric, float $value);

    public function set(Gauge $gauge, float $value);
}
