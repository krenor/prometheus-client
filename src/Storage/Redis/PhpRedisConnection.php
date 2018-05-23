<?php

namespace Krenor\Prometheus\Storage\Redis;

use Redis;

class PhpRedisConnection extends Connection
{
    /**
     * PhpRedisConnection constructor.
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
    public function hset(string $key, string $field, $value): int
    {
        return (int) $this->client->hset($key, $field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function hsetnx(string $key, string $field, $value): int
    {
        return (int) $this->client->hsetnx($key, $field, $value);
    }
}
