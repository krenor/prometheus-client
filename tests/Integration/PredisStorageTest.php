<?php

namespace Krenor\Prometheus\Tests\Integration;

use Predis\Client as Redis;
use Krenor\Prometheus\Storage\Redis\PredisConnection;
use Krenor\Prometheus\Storage\Repositories\RedisRepository;

class PredisStorageTest extends TestCase
{
    /**
     * @var RedisRepository
     */
    protected static $repository;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $redis = new Redis([
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
        ]);

        self::$repository = new RedisRepository(
            new PredisConnection($redis)
        );
    }
}
