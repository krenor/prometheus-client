<?php

namespace Krenor\Prometheus\Tests\Integration;

use Memcached;
use Krenor\Prometheus\Storage\Repositories\MemcachedRepository;

class MemcachedStorageTest extends TestCase
{
    /**
     * @var MemcachedRepository
     */
    protected static $repository;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $memcached = new Memcached;

        $memcached->addServer(
            getenv('MEMCACHED_HOST'),
            getenv('MEMCACHED_PORT')
        );

        self::$repository = new MemcachedRepository($memcached);
    }
}
