<?php

namespace ModelContextProtocol\PHP\SDK\Server;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class SSEServerTransport {
    private string $endpoint;
    private string $sessionId;

    public function __construct(string $endpoint, string $sessionId) {
        $this->endpoint = $endpoint;
        $this->sessionId = $sessionId;
    }

    public function handleMessage(string $message): void {
        // Deserialize and process JSON-RPC message here
    }

    public function close(): void {
        // Handle closing the session
    }
}
