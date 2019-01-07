<?php

namespace Krenor\Prometheus\Tests\Unit\Storage;

use Mockery as m;
use ReflectionClass;
use RuntimeException;
use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Metrics\Gauge;
use Krenor\Prometheus\Metrics\Counter;
use Krenor\Prometheus\Metrics\Summary;
use Krenor\Prometheus\Metrics\Histogram;
use Tightenco\Collect\Support\Collection;
use Krenor\Prometheus\Contracts\Repository;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Exceptions\LabelException;
use Krenor\Prometheus\Exceptions\StorageException;
use Krenor\Prometheus\Contracts\Bindings\Collector;
use Krenor\Prometheus\Tests\Stubs\InvalidCollectorStub;
use Krenor\Prometheus\Tests\Stubs\SingleLabelGaugeStub;
use Krenor\Prometheus\Tests\Stubs\SingleLabelSummaryStub;
use Krenor\Prometheus\Tests\Stubs\SingleLabelCounterStub;
use Krenor\Prometheus\Tests\Stubs\SingleLabelHistogramStub;

class StorageManagerTest extends TestCase
{
    /**
     * @test
     *
     * @group counters
     * @group storage
     */
    public function it_should_collect_counter_metric_samples()
    {
        $repository = m::mock(Repository::class);

        $repository
            ->expects('get')
            ->once()
            ->with('PHPUNIT:example_counter')
            ->andReturn(new Collection([
                '{"labels":{"example_label":"hello world"}}' => 1,
            ]));

        $storage = new StorageManager($repository, 'PHPUNIT');
        $metric = new SingleLabelCounterStub;

        $this->assertNotEmpty($storage->collect($metric));
    }

    /**
     * @test
     *
     * @group gauges
     * @group storage
     */
    public function it_should_collect_gauge_metric_samples()
    {
        $repository = m::mock(Repository::class);

        $repository
            ->expects('get')
            ->once()
            ->with('PHPUNIT:example_gauge')
            ->andReturn(new Collection([
                '{"labels":{"example_label":"hello world"}}' => 1,
            ]));

        $storage = new StorageManager($repository, 'PHPUNIT');
        $metric = new SingleLabelGaugeStub;

        $this->assertNotEmpty($storage->collect($metric));
    }

    /**
     * @test
     *
     * @group histograms
     * @group storage
     */
    public function it_should_collect_histogram_metric_samples()
    {
        $repository = m::mock(Repository::class);

        $repository
            ->expects('get')
            ->once()
            ->with('PHPUNIT:example_histogram')
            ->andReturn(new Collection([
                '{"labels":{"example_label":"hello world"}}' => 1,
            ]));

        $repository
            ->expects('get')
            ->once()
            ->with('PHPUNIT:example_histogram:SUM')
            ->andReturn(new Collection([
                '{"labels":{"example_label":"hello world"}}' => 5,
            ]));

        $storage = new StorageManager($repository, 'PHPUNIT');
        $metric = new SingleLabelHistogramStub;

        $this->assertNotEmpty($storage->collect($metric));
    }

    /**
     * @test
     *
     * @group storage
     * @group summaries
     */
    public function it_should_collect_summary_metric_samples()
    {
        $repository = m::mock(Repository::class);

        $repository
            ->expects('get')
            ->once()
            ->with('PHPUNIT:example_summary')
            ->andReturn(new Collection([
                '{"labels":{"example_label":"hello world"}}' => 'PHPUNIT:example_summary:1234:VALUES',
                '{"labels":{"example_label":"foo bar"}}'     => 'PHPUNIT:example_summary:9876:VALUES',
            ]));

        $repository
            ->expects('get')
            ->once()
            ->with('PHPUNIT:example_summary:1234:VALUES')
            ->andReturn(new Collection([1, 2, 3, 4]));

        $repository
            ->expects('get')
            ->once()
            ->with('PHPUNIT:example_summary:9876:VALUES')
            ->andReturn(new Collection([9, 8, 7, 6]));

        $storage = new StorageManager($repository, 'PHPUNIT');
        $metric = new SingleLabelSummaryStub;

        $this->assertNotEmpty($storage->collect($metric));
    }

    /** @test */
    public function it_should_throw_an_exception_if_no_collector_binding_was_found_for_the_metric()
    {
        $error = 'Could not find collector for metric.';
        $repository = m::mock(Repository::class);
        $storage = new StorageManager($repository);

        $repository->shouldReceive('get')
                   ->once()
                   ->andReturn(new Collection);

        $reflection = (new ReflectionClass($storage))->getProperty('bindings');
        $reflection->setAccessible(true);

        /** @var array $bindings */
        $bindings = $reflection->getValue($storage);

        unset($bindings['collect'][Counter::class]);

        $reflection->setValue($storage, $bindings);

        // FIXME: This is kinda tricky as the StorageException extends RuntimeException.
        // FIXME: The error is there, but it's being wrapped. Might have to revisit this later on.
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($error);

        $metric = new SingleLabelCounterStub;

        $storage->collect($metric);
    }

    /** @test */
    public function it_should_throw_an_exception_if_the_collector_binding_is_invalid()
    {
        $error = 'The collector does not fulfill the collector contract.';
        $repository = m::mock(Repository::class);
        $storage = new StorageManager($repository);

        $repository->shouldReceive('get')
                   ->once()
                   ->andReturn(new Collection);

        $reflection = (new ReflectionClass($storage))->getProperty('bindings');
        $reflection->setAccessible(true);

        /** @var array $bindings */
        $bindings = $reflection->getValue($storage);
        $bindings['collect'][Counter::class] = InvalidCollectorStub::class;

        $reflection->setValue($storage, $bindings);

        // FIXME: This is kinda tricky as the StorageException extends RuntimeException.
        // FIXME: The error is there, but it's being wrapped. Might have to revisit this later on.
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($error);

        $metric = new SingleLabelCounterStub;

        $storage->collect($metric);
    }

    /**
     * @test
     *
     * @group exceptions
     * @group storage
     */
    public function it_should_throw_a_storage_exception_when_collecting_a_metric_fails()
    {
        $error = 'Dagit nagit, nabit dagit!';
        $repository = m::mock(Repository::class);

        $repository
            ->expects('get')
            ->once()
            ->andThrow('ErrorException', $error);

        $this->expectException(StorageException::class);
        $this->expectExceptionMessage(
            "Failed to collect the samples of [Krenor\Prometheus\Tests\Stubs\SingleLabelCounterStub]: {$error}"
        );

        $storage = new StorageManager($repository);
        $metric = new SingleLabelCounterStub;

        $storage->collect($metric);
    }

    /**
     * @test
     *
     * @group storage
     */
    public function it_should_increment_a_metric()
    {
        $repository = m::mock(Repository::class);

        $repository
            ->expects('increment')
            ->once()
            ->withArgs([
                'PHPUNIT:example_counter',
                '{"labels":{"example_label":"hello world"}}',
                1,
            ]);

        $storage = new StorageManager($repository, 'PHPUNIT');
        $metric = new SingleLabelCounterStub;

        $this->assertEmpty($storage->increment($metric, 1, ['hello world']));
    }

    /**
     * @test
     *
     * @group exceptions
     * @group storage
     */
    public function it_should_throw_a_storage_exception_when_incrementing_a_metric_fails()
    {
        $error = 'OOPSIE WOOPSIE!!';
        $repository = m::mock(Repository::class);

        $repository
            ->expects('increment')
            ->once()
            ->andThrow('ErrorException', $error);

        $this->expectException(StorageException::class);
        $this->expectExceptionMessage(
            "Failed to increment [Krenor\Prometheus\Tests\Stubs\SingleLabelCounterStub] by `1`: {$error}"
        );

        $storage = new StorageManager($repository);
        $metric = new SingleLabelCounterStub;

        $storage->increment($metric, 1, ['The code monkeys at our headquarters are working VEWY HAWD to fix this']);
    }

    /**
     * @test
     *
     * @group exceptions
     * @group storage
     */
    public function it_should_not_wrap_label_exceptions_in_a_storage_exception_when_incrementing_a_metric()
    {
        $metric = new SingleLabelCounterStub;
        $storage = new StorageManager(m::mock(Repository::class));

        $this->expectException(LabelException::class);
        $this->expectExceptionMessage('Expected 1 label values but 0 were given.');

        $storage->increment($metric, 1, []);
    }

    /**
     * @test
     *
     * @group storage
     */
    public function it_should_decrement_a_metric()
    {
        $repository = m::mock(Repository::class);

        $repository
            ->expects('decrement')
            ->once()
            ->withArgs([
                'PHPUNIT:example_gauge',
                '{"labels":{"example_label":"hello world"}}',
                1,
            ]);

        $storage = new StorageManager($repository, 'PHPUNIT');
        $metric = new SingleLabelGaugeStub;

        $this->assertEmpty($storage->decrement($metric, 1, ['hello world']));
    }

    /**
     * @test
     *
     * @group exceptions
     * @group storage
     */
    public function it_should_throw_a_storage_exception_when_decrementing_a_metric_fails()
    {
        $error = 'Uwu We made a fucky wucky!!';
        $repository = m::mock(Repository::class);

        $repository
            ->expects('decrement')
            ->once()
            ->andThrow('ErrorException', $error);

        $this->expectException(StorageException::class);
        $this->expectExceptionMessage(
            "Failed to decrement [Krenor\Prometheus\Tests\Stubs\SingleLabelGaugeStub] by `1`: {$error}"
        );

        $storage = new StorageManager($repository);
        $metric = new SingleLabelGaugeStub;

        $storage->decrement($metric, 1, ['The code monkeys at our headquarters are working VEWY HAWD to fix this']);
    }

    /**
     * @test
     *
     * @group exceptions
     * @group storage
     */
    public function it_should_not_wrap_label_exceptions_in_a_storage_exception_when_decrementing_a_metric()
    {
        $metric = new SingleLabelGaugeStub;
        $storage = new StorageManager(m::mock(Repository::class));

        $this->expectException(LabelException::class);
        $this->expectExceptionMessage('Expected 1 label values but 2 were given.');

        $storage->decrement($metric, 1, [null, null]);
    }

    /**
     * @test
     *
     * @group storage
     */
    public function it_should_set_a_metric()
    {
        $repository = m::mock(Repository::class);

        $repository
            ->expects('set')
            ->once()
            ->withArgs([
                'PHPUNIT:example_gauge',
                '{"labels":{"example_label":"hello world"}}',
                42,
            ]);

        $storage = new StorageManager($repository, 'PHPUNIT');
        $metric = new SingleLabelGaugeStub;

        $this->assertEmpty($storage->set($metric, 42, ['hello world']));
    }

    /**
     * @test
     *
     * @group exceptions
     * @group storage
     */
    public function it_should_throw_a_storage_exception_when_seting_a_metric_fails()
    {
        $error = 'A wittle fucko boingo!';
        $repository = m::mock(Repository::class);

        $repository
            ->expects('set')
            ->once()
            ->andThrow('ErrorException', $error);

        $this->expectException(StorageException::class);
        $this->expectExceptionMessage(
            "Failed to set [Krenor\Prometheus\Tests\Stubs\SingleLabelGaugeStub] to `42`: {$error}"
        );

        $storage = new StorageManager($repository);
        $metric = new SingleLabelGaugeStub;

        $storage->set($metric, 42, ['The code monkeys at our headquarters are working VEWY HAWD to fix this']);
    }

    /**
     * @test
     *
     * @group exceptions
     * @group storage
     */
    public function it_should_not_wrap_label_exceptions_in_a_storage_exception_when_setting_a_metric()
    {
        $metric = new SingleLabelGaugeStub;
        $storage = new StorageManager(m::mock(Repository::class));

        $this->expectException(LabelException::class);
        $this->expectExceptionMessage('Expected 1 label values but 0 were given.');

        $storage->set($metric, 1, []);
    }

    /**
     * @test
     *
     * @group histograms
     * @group storage
     */
    public function it_should_observe_a_histogram_metric()
    {
        $repository = m::mock(Repository::class);

        $repository
            ->expects('increment')
            ->once()
            ->withArgs([
                'PHPUNIT:example_histogram',
                '{"labels":{"example_label":"hello world"},"bucket":100}',
                1,
            ]);

        $repository
            ->expects('increment')
            ->once()
            ->withArgs([
                'PHPUNIT:example_histogram:SUM',
                '{"labels":{"example_label":"hello world"}}',
                7,
            ]);

        $storage = new StorageManager($repository, 'PHPUNIT');
        $metric = new SingleLabelHistogramStub;

        $this->assertEmpty($storage->observe($metric, 7, ['hello world']));
    }

    /**
     * @test
     *
     * @group storage
     * @group summaries
     */
    public function it_should_observe_a_summary_metric()
    {
        $field = '{"labels":{"example_label":"hello world"}}';
        $identifier = crc32($field);
        $repository = m::mock(Repository::class);

        $repository
            ->expects('set')
            ->once()
            ->withArgs([
                'PHPUNIT:example_summary',
                $field,
                "PHPUNIT:example_summary:{$identifier}:VALUES",
                false,
            ]);

        $repository
            ->expects('push')
            ->once()
            ->withArgs([
                "PHPUNIT:example_summary:{$identifier}:VALUES",
                13,
            ]);

        $storage = new StorageManager($repository, 'PHPUNIT');
        $metric = new SingleLabelSummaryStub;

        $this->assertEmpty($storage->observe($metric, 13, ['hello world']));
    }

    /**
     * @test
     *
     * @group exceptions
     * @group storage
     */
    public function it_should_throw_a_storage_exception_when_observing_a_metric_fails()
    {
        $error = 'OOPSIE WOOPSIE!! Uwu We made a fucky wucky!! A wittle fucko boingo!';
        $repository = m::mock(Repository::class);

        $repository
            ->expects('increment')
            ->once()
            ->andThrow('ErrorException', $error);

        $this->expectException(StorageException::class);
        $this->expectExceptionMessage(
            "Failed to observe [Krenor\Prometheus\Tests\Stubs\SingleLabelHistogramStub] with `5`: {$error}"
        );

        $storage = new StorageManager($repository);
        $metric = new SingleLabelHistogramStub;

        $storage->observe($metric, 5, ['This twitter post never gets boring']);
    }

    /**
     * @test
     *
     * @group exceptions
     * @group storage
     */
    public function it_should_not_wrap_label_exceptions_in_a_storage_exception_when_observing_a_metric()
    {
        $metric = new SingleLabelHistogramStub;
        $storage = new StorageManager(m::mock(Repository::class));

        $this->expectException(LabelException::class);
        $this->expectExceptionMessage('Expected 1 label values but 3 were given.');

        $storage->observe($metric, 1, [null, null, null]);
    }

    /** @test
     *
     * @group storage
     */
    public function it_should_be_possible_to_bind_custom_collectors_to_metric_types()
    {
        $storage = new StorageManager(m::mock(Repository::class));
        $collector = get_class(m::mock(Collector::class));

        $reflection = (new ReflectionClass($storage))->getProperty('bindings');
        $reflection->setAccessible(true);

        /** @var array $bindings */
        $bindings = $reflection->getValue($storage);

        $this->assertArrayHasKey('collect', $bindings);
        $this->assertArrayHasKey(Counter::class, $bindings['collect']);
        $this->assertArrayHasKey(Gauge::class, $bindings['collect']);
        $this->assertArrayHasKey(Histogram::class, $bindings['collect']);
        $this->assertArrayHasKey(Summary::class, $bindings['collect']);

        $storage->bind(Counter::class, $collector);

        $bindings = $reflection->getValue($storage);

        $this->assertSame($bindings['collect'][Counter::class], $collector);
    }
}
