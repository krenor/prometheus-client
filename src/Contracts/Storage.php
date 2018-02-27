<?php

namespace Krenor\Prometheus\Contracts;

use Krenor\Prometheus\Contracts\Types\Observable;
use Krenor\Prometheus\Contracts\Types\Decrementable;
use Krenor\Prometheus\Contracts\Types\Incrementable;

interface Storage
{
    public function increment(Incrementable $metric);

    public function decrement(Decrementable $metric);

    public function observe(Observable $metric);
}
