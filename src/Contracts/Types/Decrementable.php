<?php

namespace Krenor\Prometheus\Contracts\Types;

interface Decrementable
{
    public function decrement();

    public function decrementBy(float $value);
}
