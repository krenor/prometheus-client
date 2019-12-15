<?php

namespace Krenor\Prometheus\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Krenor\Prometheus\PushGateway;
use GuzzleHttp\Handler\MockHandler;
use Krenor\Prometheus\CollectorRegistry;
use Krenor\Prometheus\Renderer\TextRenderer;

class PushGatewayTest extends TestCase
{
    /**
     * @var HandlerStack
     */
    private $handler;

    /**
     * @var PushGateway
     */
    private $gateway;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new MockHandler;
        $this->gateway = new PushGateway(new Client([
            'handler' => HandlerStack::create($this->handler),
        ]), new CollectorRegistry);
    }

    /** @test */
    public function it_should_send_metrics_through_a_post_request()
    {
        $this->handler->append(new Response(200));

        $this->assertTrue($this->gateway->add('example', 'default'));

        $request = $this->handler->getLastRequest();

        $this->assertSame('job/example/instance/default', (string) $request->getUri());
        $this->assertNotEmpty($request->getBody()->getSize());
        $this->assertSame([TextRenderer::CONTENT_TYPE], $request->getHeader('content-type'));
    }

    /** @test */
    public function it_should_send_metrics_through_a_put_request()
    {
        $this->handler->append(new Response(204));

        $this->assertFalse($this->gateway->replace('example', 'default'));

        $request = $this->handler->getLastRequest();

        $this->assertSame('job/example/instance/default', (string) $request->getUri());
        $this->assertNotEmpty($request->getBody()->getSize());
        $this->assertSame([TextRenderer::CONTENT_TYPE], $request->getHeader('content-type'));
    }

    /** @test */
    public function it_should_send_a_deletion_request()
    {
        $this->handler->append(new Response(200));

        $this->assertTrue($this->gateway->delete('example'));

        $request = $this->handler->getLastRequest();

        $this->assertSame('job/example', (string) $request->getUri());
        $this->assertEmpty($request->getBody()->getSize());
        $this->assertSame([TextRenderer::CONTENT_TYPE], $request->getHeader('content-type'));
    }
}
