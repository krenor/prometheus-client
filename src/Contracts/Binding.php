<?php

namespace Krenor\Prometheus\Contracts;

abstract class Binding
{
    /**
     * Binding constructor.
     *
     * @param Repository $repository
     * @param string $key
     */
    public function __construct(protected Repository $repository, protected string $key)
    {
        //
    }
}
