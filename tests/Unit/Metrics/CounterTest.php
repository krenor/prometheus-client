<?php

namespace Krenor\Prometheus\Tests\Unit\Metrics;

use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Metrics\Counter;
use Krenor\Prometheus\Exceptions\PrometheusException;

class CounterTest extends TestCase
{
    /**
     * @test
     *
     * @group exceptions
     * @group counters
     */
    public function it_should_throw_an_exception_if_being_incremented_with_a_negative_number()
    {
        $this->expectException(PrometheusException::class);
        $this->expectExceptionMessage('Counters can only be incremented by non-negative amounts.');

        $counter = new class extends Counter {
            protected string $name = 'counter';
        };

        $counter->incrementBy(-42);
    }
}
