<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Builders;

use ReflectionProperty;
use Krenor\Prometheus\Sample;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Tests\Stubs\SingleLabelSummaryStub;
use Krenor\Prometheus\Storage\Builders\SummarySamplesBuilder;

class SummarySamplesBuilderTest extends TestCase
{
    /**
     * @test
     *
     * @group builders
     * @group summaries
     */
    public function it_should_create_summary_samples()
    {
        $summary = new SingleLabelSummaryStub;

        $labels = $summary
            ->labels()
            ->combine(['hello world'])
            ->toJson();

        $values = new Collection([-13, -13, -12, -10, -5, 3, 5, 8, 10]);

        $builder = new SummarySamplesBuilder($summary, new Collection([
            "{\"labels\":{$labels}}" => $values,
        ]));

        /** @var Sample[] $samples */
        $samples = $builder->samples();

        $this->assertCount($summary->quantiles()->count() + 2, $samples);

        $this->assertSame(.2, $samples[0]->labels()->get('quantile'));
        // Only needs to match for the first one since the following ones reuse them
        $this->assertSame($labels, $samples[0]->labels()->except('quantile')->toJson());
        $this->assertEquals(-13, $samples[0]->value());

        $this->assertSame(.4, $samples[1]->labels()->get('quantile'));
        $this->assertEquals(-10, $samples[1]->value());

        $this->assertSame(.6, $samples[2]->labels()->get('quantile'));
        $this->assertEquals(3, $samples[2]->value());

        $this->assertSame(.8, $samples[3]->labels()->get('quantile'));
        $this->assertEquals(8, $samples[3]->value());

        $this->assertContains('_count', $samples[4]->name());
        $this->assertNull($samples[4]->labels()->get('quantile'));
        $this->assertEquals($values->count(), $samples[4]->value());

        $this->assertContains('_sum', $samples[5]->name());
        $this->assertNull($samples[5]->labels()->get('quantile'));
        $this->assertEquals($values->sum(), $samples[5]->value());
    }

    /**
     * @test
     *
     * @group builders
     * @group summaries
     */
    public function it_should_initialize_summaries_without_labels()
    {
        $summary = new SingleLabelSummaryStub;

        $reflection = new ReflectionProperty($summary, 'labels');
        $reflection->setAccessible(true);
        $reflection->setValue($summary, []);

        $builder = new SummarySamplesBuilder($summary, new Collection);

        /** @var Sample[] $samples */
        $samples = $builder->samples();

        $this->assertCount($summary->quantiles()->count() + 2, $samples);

        $this->assertEquals(0, $samples[0]->value()); // .2 Quantile
        // Only needs to match for the first one since the following ones reuse them
        $this->assertEmpty($samples[0]->labels()->except('quantile'));
        $this->assertEquals(0, $samples[1]->value()); // .4 Quantile
        $this->assertEquals(0, $samples[2]->value()); // .6 Quantile
        $this->assertEquals(0, $samples[3]->value()); // .8 Quantile
        $this->assertEquals(0, $samples[4]->value()); // Count
        $this->assertEquals(0, $samples[5]->value()); // Sum
    }
}
