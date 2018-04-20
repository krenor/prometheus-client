<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Collectors;

use Krenor\Prometheus\Sample;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Tests\Stubs\MultipleLabelsSummaryStub;
use Krenor\Prometheus\Storage\Collectors\SummarySamplesCollector;

class SummarySamplesCollectorTest extends TestCase
{
    /**
     * @test
     *
     * @group collectors
     * @group summaries
     */
    public function it_should_collect_summary_samples()
    {
        $metric = new MultipleLabelsSummaryStub;

        $labels = [
            $metric->labels()->combine(['foo', 'bar', 'baz'])->toJson(),
            $metric->labels()->combine(['bar', 'baz', 'foo'])->toJson(),
            $metric->labels()->combine(['baz', 'foo', 'bar'])->toJson(),
        ];

        $values = new Collection([-15, -5, 12, 7, 14, -2, -3, 6]);

        /** @var Sample[] $samples */
        $samples = (new SummarySamplesCollector($metric, new Collection([
            "{\"labels\":{$labels[0]}}" => $values,
            "{\"labels\":{$labels[1]}}" => $values,
            "{\"labels\":{$labels[2]}}" => $values,
        ])))->collect();

        $this->assertCount(21, $samples);
        $this->assertEquals(-15, $samples[0]->value()); // .1 Quantile
        $this->assertEquals(-3, $samples[1]->value()); // .3 Quantile
        $this->assertEquals(2, $samples[2]->value()); // .5 Quantile
        $this->assertEquals(7, $samples[3]->value()); // .7 Quantile
        $this->assertEquals(14, $samples[4]->value()); // .9 Quantile
        $this->assertEquals($values->count(), $samples[5]->value()); // Count
        $this->assertEquals($values->sum(), $samples[6]->value()); // Sum
    }
}
