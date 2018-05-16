<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Repositories;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Exceptions\StorageException;
use Krenor\Prometheus\Storage\Repositories\SimpleRepository;

class SimpleRepositoryTest extends TestCase
{
    /**
     * @var SimpleRepository|m\MockInterface
     */
    private $repository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->repository = m::mock(SimpleRepository::class)
                             ->makePartial()
                             ->shouldAllowMockingProtectedMethods();
    }

    /**
     * @test
     *
     * @group repositories
     */
    public function it_should_wrap_stored_data_as_a_collection()
    {
        $this->repository
            ->shouldReceive('retrieve')
            ->once()
            ->with('key')
            ->andReturn(['foo', 'bar']);

        $this->assertCount(2, $this->repository->get('key'));
    }

    /**
     * @test
     *
     * @group repositories
     */
    public function it_should_return_a_new_collection_when_retrieve_does_not_return_any_stored_data()
    {
        $this->repository
            ->shouldReceive('retrieve')
            ->once()
            ->with('key')
            ->andReturnFalse();

        $this->assertSame([], $this->repository->get('key')->toArray());
    }

    /**
     * @test
     *
     * @group exceptions
     * @group repositories
     */
    public function it_should_throw_a_storage_exception_when_storing_fails()
    {
        $this->repository
            ->shouldReceive('retrieve')
            ->once()
            ->with('key')
            ->andReturnFalse();

        $this->repository
            ->shouldReceive('store')
            ->once()
            ->withAnyArgs()
            ->andReturnFalse();

        $this->expectException(StorageException::class);
        $this->expectExceptionMessage('The storage returned `false`.');

        $this->repository->push('key', 1);
    }
}
