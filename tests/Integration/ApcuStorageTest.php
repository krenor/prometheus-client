<?php

namespace Krenor\Prometheus\Tests\Integration;

use Krenor\Prometheus\Storage\Repositories\ApcuRepository;

class ApcuStorageTest extends TestCase
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

        self::$repository = new ApcuRepository;
    }
}
