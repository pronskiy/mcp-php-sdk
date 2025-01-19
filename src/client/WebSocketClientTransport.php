<?php

namespace ModelContextProtocol\Client;

use ModelContextProtocol\Types\JSONRPCRequest;
use ModelContextProtocol\Types\JSONRPCResponse;
use React\EventLoop\Factory;
use Ratchet\Client\WebSocket;
use Ratchet\Client\Connector as ClientConnector;

class WebSocketClientTransport implements Transport {
    private $url;
    private $connection;
    private $loop;

    public function __construct(string $url, ?\React\EventLoop\LoopInterface $loop = null) {
        $this->url = $url;
        $this->loop = $loop ?? Factory::create();
    }

    public function connect(): void {
        $connector = new ClientConnector($this->loop);
        $connector($this->url)->then(
            function(WebSocket $conn) {
                $this->connection = $conn;

                $conn->on('message', function($msg) {
                    // Handle incoming messages
                    $response = JSONRPCResponse::fromJson((string)$msg);
                });
            },
            function ($e) {
                throw new \RuntimeException("Could not connect: {$e->getMessage()}");
            }
        );
    }

    public function sendRequest(JSONRPCRequest $request): JSONRPCResponse {
        if (!$this->connection) {
            throw new \RuntimeException("Not connected");
        }

        $jsonRequest = json_encode($request);
        $this->connection->send($jsonRequest);

        return new Promise(function ($resolve, $reject) {
            $this->connection->once('message', function($msg) use ($resolve) {
                $response = JSONRPCResponse::fromJson((string)$msg);
                $resolve($response);
            });
        });
    }

    public function close(): void {
        if ($this->connection) {
            $this->connection->close();
        }
        $this->loop->stop();
    }
}
