<?php

namespace ModelContextProtocol\Client;

use ModelContextProtocol\Types\JSONRPCRequest;
use ModelContextProtocol\Types\JSONRPCResponse;
use React\EventLoop\Factory;
use React\Http\Browser;
use React\Promise\Promise;

class SSEClientTransport implements Transport {
    private $baseUrl;
    private $sessionId;
    private $browser;
    private $loop;

    public function __construct(string $baseUrl, ?\React\EventLoop\LoopInterface $loop = null) {
        $this->baseUrl = $baseUrl;
        $this->loop = $loop ?? Factory::create();
        $this->browser = new Browser($this->loop);
        $this->sessionId = uniqid();
    }

    public function connect(): void {
        // Initialize SSE connection
        $this->browser->get($this->baseUrl . '/sse?sessionId=' . $this->sessionId)
            ->then(
                function ($response) {
                    // Connection established
                },
                function ($error) {
                    throw new \RuntimeException('Failed to connect: ' . $error->getMessage());
                }
            );
    }

    public function sendRequest(JSONRPCRequest $request): JSONRPCResponse {
        $jsonRequest = json_encode($request);

        return $this->browser->post(
            $this->baseUrl . '/message?sessionId=' . $this->sessionId,
            ['Content-Type' => 'application/json'],
            $jsonRequest
        )->then(
            function ($response) {
                return JSONRPCResponse::fromJson((string)$response->getBody());
            },
            function ($error) {
                throw new \RuntimeException('Request failed: ' . $error->getMessage());
            }
        );
    }

    public function close(): void {
        $this->loop->stop();
    }
}
