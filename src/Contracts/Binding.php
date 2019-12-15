<?php

namespace Krenor\Prometheus\Contracts;

abstract class Binding
{
    /**
     * @var Repository
     */
    protected Repository $repository;

    /**
     * @var string
     */
    protected string $key;

    /**
     * Binding constructor.
     *
     * @param Repository $repository
     * @param string $key
     */
    public function __construct(Repository $repository, string $key)
    {
        $this->repository = $repository;
        $this->key = $key;
    }
}
