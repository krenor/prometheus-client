<?php

namespace Krenor\Prometheus\Contracts;

use Tightenco\Collect\Support\Collection;

interface Metric
{
    /**
     * @return string
     */
    public function key(): string;

    /**
     * @return string
     */
    public function type(): string;

    /**
     * @return string|null
     */
    public function namespace(): ?string;

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return string
     */
    public function description(): string;

    /**
     * @return Collection
     */
    public function labels(): Collection;

    /**
     * @return Storage
     */
    public static function storage(): Storage;
}
