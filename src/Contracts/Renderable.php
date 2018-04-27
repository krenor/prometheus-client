<?php

namespace Krenor\Prometheus\Contracts;

use Tightenco\Collect\Support\Collection;

interface Renderable
{
    /**
     * @param Collection $metrics
     *
     * @return string
     */
    public function render(Collection $metrics): string;
}
