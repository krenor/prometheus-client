<?php

namespace Krenor\Prometheus\Tests\Unit\Metrics\Concerns;

use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Metrics\Concerns\TracksExecutionTime;

class TracksExecutionTimeTest extends TestCase
{
    private object $object;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->object = $this->createObjectForTrait();
    }

    /**
     * @test
     *
     * @group traits
     */
    public function it_should_track_execution_time_of_a_closure()
    {
        $start = microtime(true);

        $track = $this->object->chronometer();

        usleep(50000);

        $track();

        $delta = microtime(true) - $start;

        $this->assertNotSame(0.0, $this->object->value);
        // Allow an error margin between comparison
        $this->assertLessThan(.0005, abs($this->object->value - $delta));
        $this->assertEmpty($this->object->labels);
    }

    /**
     * @test
     *
     * @group traits
     */
    public function it_should_round_the_tracked_time_to_given_precision()
    {
        $precision = 2;

        $track = $this->object->chronometer([], $precision);

        usleep(50000);

        $track();

        $this->assertSame($precision, strlen(substr(
            strstr($this->object->value, '.'),
            1
        )));
    }

    /**
     * @test
     *
     * @group traits
     */
    public function it_should_merge_the_given_start_and_end_labels()
    {
        $track = $this->object->chronometer(['foo', 'bar']);

        $track();

        $this->assertSame($this->object->labels, [
            'foo',
            'bar',
        ]);

        $track(['baz', 'qux']);

        $this->assertSame($this->object->labels, [
            'foo',
            'bar',
            'baz',
            'qux',
        ]);
    }

    /**
     * @return object
     */
    private function createObjectForTrait()
    {
        return new class {
            use TracksExecutionTime;

            public float $value = 0.0;

            public array $labels = [];

            /**
             * {@inheritdoc}
             */
            protected function track(float $value, array $labels): void
            {
                $this->value = $value;
                $this->labels = $labels;
            }
        };
    }
}
