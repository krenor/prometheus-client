<?php

namespace Krenor\Prometheus\Tests\Integration;

use Krenor\Prometheus\Contracts\Metric;
use Krenor\Prometheus\Storage\StorageManager;
use Krenor\Prometheus\Storage\Repositories\ApcuRepository;

class ApcuStorageTest extends TestCase
{
    /**
     * @var ApcRepository
     */
    private $repository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->repository = new ApcuRepository;

        Metric::storeUsing(new StorageManager($this->repository));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->repository->flush();
    }
}
