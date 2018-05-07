<?php

namespace Krenor\Prometheus\Tests\Unit\Storage\Repositories;

use Mockery as m;
use Predis\Client;
use Predis\Response\Status;
use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\Contracts\Repository;
use Krenor\Prometheus\Storage\Repositories\RedisRepository;

class RedisRepositoryTest extends TestCase
{
    /**
     * @var m\MockInterface
     */
    private $redis;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->redis = m::mock(Client::class);
        $this->repository = new RedisRepository($this->redis);
    }

    /**
     * @test
     *
     * @group repositories
     */
    public function it_should_retrieve_items_from_a_hash_or_a_list_based_on_the_key()
    {
        $hash = 'some_key';
        $list = 'some_key:VALUES';

        $this->redis
            ->shouldReceive('hgetall')
            ->once()
            ->with($hash)
            ->andReturn([1]);

        $this->redis
            ->shouldReceive('lrange')
            ->once()
            ->with($list, 0, -1)
            ->andReturn([1, 2, 3]);

        $this->assertCount(1, $this->repository->get($hash));
        $this->assertCount(3, $this->repository->get($list));
    }

    /**
     * @test
     *
     * @group repositories
     */
    public function it_should_increment_a_hash_value()
    {
        $this->redis
            ->shouldReceive('hincrbyfloat')
            ->once()
            ->withArgs([
                'key',
                'field',
                1.5,
            ]);

        $this->assertEmpty($this->repository->increment('key', 'field', 1.5));
    }

    /**
     * @test
     *
     * @group repositories
     */
    public function it_should_decrement_a_hash_value()
    {
        $this->redis
            ->shouldReceive('hincrbyfloat')
            ->once()
            ->withArgs([
                'key',
                'field',
                -1.5,
            ]);

        $this->assertEmpty($this->repository->decrement('key', 'field', -1.5));
    }

    /**
     * @test
     *
     * @group repositories
     */
    public function it_should_set_a_hash_value_with_optional_override_based_on_the_key()
    {
        $this->redis
            ->shouldReceive('hset')
            ->once()
            ->withArgs([
                'key',
                'field',
                'value',
            ]);

        $this->redis
            ->shouldReceive('hsetnx')
            ->once()
            ->withArgs([
                'key',
                'field',
                'value',
            ]);

        $this->assertEmpty($this->repository->set('key', 'field', 'value'));
        $this->assertEmpty($this->repository->set('key', 'field', 'value', false));
    }

    /**
     * @test
     *
     * @group repositories
     */
    public function it_should_append_a_value_to_a_list()
    {
        $this->redis
            ->shouldReceive('rpush')
            ->withArgs([
                'key',
                1.75,
            ]);

        $this->assertEmpty($this->repository->push('key', 1.75));
    }

    /**
     * @test
     *
     * @group repositories
     */
    public function it_should_flush_the_data()
    {
        $this->redis
            ->shouldReceive('flushdb')
            ->andReturn(new Status('OK'));

        $this->assertTrue($this->repository->flush());
    }
}
