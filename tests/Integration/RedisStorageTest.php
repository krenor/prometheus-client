<?php

namespace Krenor\Prometheus\Tests\Integration;

use Predis\Client as Redis;
use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Storage\RedisStorage;

class RedisStorageTest extends TestCase
{
    /**
     * @var Redis
     */
    private $redis;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->redis = new Redis([
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
        ]);

        Metric::storeUsing(new RedisStorage($this->redis));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->redis->flushall();
    }
}
