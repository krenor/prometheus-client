<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Concerns;

use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Exceptions\LabelException;
use Krenor\Prometheus\Storage\Concerns\StoresMetrics;
use Krenor\Prometheus\Tests\Stubs\SingleLabelHistogramStub;

class StoresMetricsTest extends TestCase
{
    /**
     * @var object
     */
    private $object;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->createObjectForTrait();
    }

    /**
     * @test
     *
     * @group traits
     */
    public function it_should_combine_label_values_with_their_names()
    {
        $metric = new SingleLabelHistogramStub;

        $this->assertSame(
            ['labels' => ['example_label' => 'hello world']],
            $this->object->getLabels($metric, ['hello world'])->toArray()
        );
    }

    /**
     * @test
     *
     * @group exceptions
     * @group traits
     */
    public function it_should_raise_an_exception_when_the_amount_of_label_names_and_label_values_differ()
    {
        $metric = new SingleLabelHistogramStub;

        $this->expectException(LabelException::class);
        $this->expectExceptionMessage('Expected 1 label values but 0 were given.');

        $this->object->getLabels($metric, []);
    }

    /**
     * @test
     *
     * @group histograms
     * @group traits
     */
    public function it_determine_the_bucket_based_on_the_value_and_the_available_buckets_of_a_histogram()
    {
        $metric = new SingleLabelHistogramStub;

        $this->assertSame(['bucket' => 100], $this->object->getBucket($metric, 11));
        $this->assertSame(['bucket' => 250], $this->object->getBucket($metric, 222));
        $this->assertSame(['bucket' => 600], $this->object->getBucket($metric, 555));
        $this->assertSame(['bucket' => '+Inf'], $this->object->getBucket($metric, 888));
    }

    /**
     * @return object
     */
    private function createObjectForTrait()
    {
        return new class
        {
            use StoresMetrics;

            public function getLabels(...$args)
            {
                return $this->labeled(...$args);
            }

            public function getBucket(...$args)
            {
                return $this->bucket(...$args);
            }
        };
    }
}
