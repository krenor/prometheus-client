<?php

namespace Krenor\Prometheus;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use Krenor\Prometheus\Renderer\TextRenderer;

class PushGateway
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var CollectorRegistry
     */
    protected $registry;

    /**
     * PushGateway constructor.
     *
     * @param Client $client
     * @param CollectorRegistry $registry
     */
    public function __construct(Client $client, CollectorRegistry $registry)
    {
        $this->client = $client;
        $this->registry = $registry;
    }

    /**
     * @param string $job
     * @param string|null $instance
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return bool
     */
    public function add(string $job, ?string $instance = null): bool
    {
        return $this->request('POST', ...func_get_args());
    }

    /**
     * @param string $job
     * @param string|null $instance
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return bool
     */
    public function replace(string $job, ?string $instance = null): bool
    {
        return $this->request('PUT', ...func_get_args());
    }

    /**
     * @param string $job
     * @param string|null $instance
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return bool
     */
    public function delete(string $job, ?string $instance = null): bool
    {
        return $this->request('DELETE', ...func_get_args());
    }

    /**
     * @param string $method
     * @param string $job
     * @param string|null $instance
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return bool
     */
    protected function request(string $method, string $job, ?string $instance = null): bool
    {
        $request = new Request($method, $instance === null ? "job/{$job}" : "job/{$job}/instance/{$instance}");
        $options = [
            'headers' => [
                'Content-Type' => TextRenderer::CONTENT_TYPE,
            ],
        ];

        if ($method !== 'DELETE') {
            $options['body'] = stream_for(
                (new TextRenderer)
                    ->render($this->registry->collect())
            );
        }

        return $this
                ->client
                ->send($request, $options)
                ->getStatusCode() === 202;
    }
}
