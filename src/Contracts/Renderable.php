<?php

namespace Krenor\Prometheus\Contracts;

use Tightenco\Collect\Support\Collection;

interface Renderable
{
    public function render(Collection $metrics);
}
