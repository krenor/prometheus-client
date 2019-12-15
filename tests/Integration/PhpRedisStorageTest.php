<?php

namespace Krenor\Prometheus\Tests\Integration;

use Redis;
use Krenor\Prometheus\Storage\Redis\PhpRedisConnection;
use Krenor\Prometheus\Storage\Repositories\RedisRepository;

class PhpRedisStorageTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $redis = new Redis;
        $redis->connect(
            getenv('REDIS_HOST'),
            getenv('REDIS_PORT')
        );

        self::$repository = new RedisRepository(
            new PhpRedisConnection($redis)
        );
    }
}
