<?php

namespace Krenor\Prometheus\Storage\Redis;

use Predis\Client as Redis;

class PredisConnection extends Connection
{
    /**
     * PredisConnection constructor.
     *
     * @param Redis $redis
     */
    public function __construct(Redis $redis)
    {
        $this->client = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function flushdb(): bool
    {
        return (bool) $this->client->flushdb();
    }
}
