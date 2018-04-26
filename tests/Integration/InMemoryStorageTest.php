<?php

namespace Krenor\Prometheus\Tests\Integration;

use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Storage\InMemoryStorage;

class InMemoryStorageTest extends TestCase
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

        Metric::storeUsing(new InMemoryStorage);
    }
}
