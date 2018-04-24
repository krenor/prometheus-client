<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Collectors;

use Krenor\Prometheus\Sample;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Tests\Stubs\SingleLabelSummaryStub;
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
        $metric = new SingleLabelSummaryStub;

        $labels = new Collection([
            $metric->labels()->combine(['hello world'])->toJson(),
            $metric->labels()->combine(['lorem ipsum'])->toJson(),
            $metric->labels()->combine(['fizz buzz'])->toJson(),
        ]);

        $values = [
            new Collection([-13, -13, -12, -10, -5, 3, 5, 8, 10]),
            new Collection([14, -6, 7, 8, 13, 3, 2, 15, -14, -7]),
            new Collection([-15, 11, 11, 8, -13, -13, 12, 14, -2, -12, 7]),
        ];

        /** @var Sample[] $samples */
        $samples = (new SummarySamplesCollector($metric, new Collection([
            "{\"labels\":{$labels[0]}}" => $values[0],
            "{\"labels\":{$labels[1]}}" => $values[1],
            "{\"labels\":{$labels[2]}}" => $values[2],
        ])))->collect();

        $this->assertCount(($metric->quantiles()->count() + 2) * $labels->count(), $samples);

        // Labeled with ['fizz buzz']
        $this->assertSame($labels[2], $samples[0]->labels()->forget('quantile')->toJson());
        $this->assertEquals(-13, $samples[0]->value()); // .2 Quantile
        $this->assertEquals(-2, $samples[1]->value()); // .4 Quantile
        $this->assertEquals(8, $samples[2]->value()); // .6 Quantile
        $this->assertEquals(11, $samples[3]->value()); // .8 Quantile
        $this->assertEquals($values[2]->count(), $samples[4]->value()); // Count
        $this->assertEquals($values[2]->sum(), $samples[5]->value()); // Sum

        // Labeled with ['hello world']
        $this->assertSame($labels[0], $samples[6]->labels()->forget('quantile')->toJson());
        $this->assertEquals(-13, $samples[6]->value()); // .2 Quantile
        $this->assertEquals(-10, $samples[7]->value()); // .4 Quantile
        $this->assertEquals(3, $samples[8]->value()); // .6 Quantile
        $this->assertEquals(8, $samples[9]->value()); // .8 Quantile
        $this->assertEquals($values[0]->count(), $samples[10]->value()); // Count
        $this->assertEquals($values[0]->sum(), $samples[11]->value()); // Sum

        // Labeled with ['lorem ipsum']
        $this->assertSame($labels[1], $samples[12]->labels()->forget('quantile')->toJson());
        $this->assertSame(-6.5, $samples[12]->value()); // .2 Quantile
        $this->assertSame(2.5, $samples[13]->value()); // .4 Quantile
        $this->assertSame(7.5, $samples[14]->value()); // .6 Quantile
        $this->assertSame(13.5, $samples[15]->value()); // .8 Quantile
        $this->assertEquals($values[1]->count(), $samples[16]->value()); // Count
        $this->assertEquals($values[1]->sum(), $samples[17]->value()); // Sum
    }
}
