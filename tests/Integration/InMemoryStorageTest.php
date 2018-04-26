<?php

namespace Krenor\Prometheus\Tests\Integration;

use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Storage\Repositories\InMemoryRepository;

class InMemoryStorageTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        Metric::storeUsing(new StorageManager(new InMemoryRepository));
    }
}
