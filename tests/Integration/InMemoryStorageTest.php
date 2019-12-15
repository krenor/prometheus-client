<?php

namespace Krenor\Prometheus\Tests\Integration;

use Krenor\Prometheus\Storage\Repositories\InMemoryRepository;

class InMemoryStorageTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$repository = new InMemoryRepository;
    }
}
