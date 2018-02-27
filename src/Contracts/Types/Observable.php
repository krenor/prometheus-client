<?php

namespace Krenor\Prometheus\Contracts\Types;

interface Observable
{
    public function observe(float $value);
}
