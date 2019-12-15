<?php

namespace Krenor\Prometheus\Tests\Integration;

use Krenor\Prometheus\Storage\Repositories\InMemoryRepository;

class InMemoryStorageTest extends TestCase
{
    /**
     * @var InMemoryRepository
     */
    protected static $repository;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$repository = new InMemoryRepository;
    }
}
