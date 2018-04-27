<?php

namespace Krenor\Prometheus\Tests\Integration;

use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Storage\Repositories\InMemoryRepository;

class InMemoryStorageTest extends TestCase
{
    /**
     * @var InMemoryRepository
     */
    private $repository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->repository = new InMemoryRepository;

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
