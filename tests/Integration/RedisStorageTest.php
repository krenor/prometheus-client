<?php

namespace Krenor\Prometheus\Tests\Integration;

use Predis\Client as Redis;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Storage\Repositories\RedisRepository;

class RedisStorageTest extends TestCase
{
    /**
     * @var RedisRepository
     */
    private $repository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->repository = new RedisRepository(new Redis([
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
        ]));

        Metric::storeUsing(new StorageManager($this->repository));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->repository->flush();
    }
}
