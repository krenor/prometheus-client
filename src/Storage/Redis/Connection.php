<?php

namespace Krenor\Prometheus\Storage\Redis;

abstract class Connection
{
    /**
     * @var \Predis\Client
     */
    protected $client;

    /**
     * @see https://redis.io/commands/hgetall
     *
     * @param string $key
     *
     * @return array
     */
    public function hgetall(string $key): array
    {
        return $this->client->hgetall($key);
    }

    /**
     * @see https://redis.io/commands/lrange
     *
     * @param string $key
     * @param int $start
     * @param int $stop
     *
     * @return array
     */
    public function lrange(string $key, int $start, int $stop): array
    {
        return $this->client->lrange($key, $start, $stop);
    }

    /**
     * @see https://redis.io/commands/hincrbyfloat
     *
     * @param string $key
     * @param string $field
     * @param float $value
     *
     * @return float
     */
    public function hincrbyfloat(string $key, string $field, float $value): float
    {
        return $this->client->hincrbyfloat($key, $field, $value);
    }

    /**
     * @see https://redis.io/commands/hset
     *
     * @param string $key
     * @param string $field
     * @param mixed $value
     *
     * @return int
     */
    public function hset(string $key, string $field, mixed $value): int
    {
        return $this->client->hset($key, $field, $value);
    }

    /**
     * @see https://redis.io/commands/rpush
     *
     * @param string $key
     * @param array $values
     *
     * @return int
     */
    public function rpush(string $key, array $values): int
    {
        return $this->client->rpush($key, ...$values);
    }

    /**
     * @see https://redis.io/commands/flushdb
     *
     * @return bool
     */
    public function flushdb(): bool
    {
        return $this->client->flushdb();
    }
}
