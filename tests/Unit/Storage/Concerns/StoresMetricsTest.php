<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Concerns;

use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Exceptions\LabelException;
use Krenor\Prometheus\Storage\Concerns\StoresMetrics;
use Krenor\Prometheus\Tests\Stubs\SingleLabelCounterStub;

class StoresMetricsTest extends TestCase
{
    /**
     * @var object
     */
    private $object;

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
    public function it_should_combine_label_values_with_their_names()
    {
        $metric = new SingleLabelCounterStub;

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
        $metric = new SingleLabelCounterStub;

        $this->expectException(LabelException::class);
        $this->expectExceptionMessage('Expected 1 label values but 0 were given.');

        $this->object->getLabels($metric, []);
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
        };
    }
}
