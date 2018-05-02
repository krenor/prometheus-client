<?php

namespace Krenor\Prometheus\Tests\Integration;

use Predis\Client as Redis;
use Krenor\Prometheus\Storage\Repositories\RedisRepository;

class RedisStorageTest extends TestCase
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

        self::$repository = new RedisRepository(new Redis([
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
        ]));
    }
}
