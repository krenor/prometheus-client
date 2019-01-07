<?php

namespace Krenor\Prometheus\Tests\Stubs;

class InvalidCollectorStub
{
    /**
     * @return array
     */
    public function collect(): array
    {
        return [];
    }
}
