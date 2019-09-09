<?php

namespace Krenor\Prometheus\Tests\Integration;

use Krenor\Prometheus\Storage\Repositories\ApcuRepository;

class ApcuStorageTest extends TestCase
{
    /**
     * @var ApcuRepository
     */
    protected static $repository;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$repository = new ApcuRepository;
    }
}
