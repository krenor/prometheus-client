<?php

namespace Krenor\Prometheus\Tests\Integration;

use Memcached;
use Krenor\Prometheus\Storage\Repositories\MemcachedRepository;

class MemcachedStorageTest extends TestCase
{
    /**
     * @var ApcRepository
     */
    protected static $repository;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
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
