<?php

namespace Krenor\Prometheus\Contracts\Types;

interface Incrementable
{
    public function increment();

    public function incrementBy(float $value);
}
