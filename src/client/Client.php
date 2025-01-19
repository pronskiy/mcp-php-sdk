<?php

namespace ModelContextProtocol\Client;

use ModelContextProtocol\Types\Implementation;
use ModelContextProtocol\Types\ClientOptions;
use ModelContextProtocol\Types\JSONRPCRequest;
use ModelContextProtocol\Types\JSONRPCResponse;

class Client {
    private $transport;
    private $implementation;
    private $options;
    private $loop;

    public function __construct(Implementation $implementation, ClientOptions $options, ?\React\EventLoop\LoopInterface $loop = null) {
        $this->implementation = $implementation;
        $this->options = $options;
        $this->loop = $loop ?? \React\EventLoop\Factory::create();
    }

    public function connect($transport): void {
        $this->transport = $transport;
        // Initialize connection
        $this->transport->connect();
    }

    public function request(array $params, ?string $schema = null): \React\Promise\PromiseInterface {
        $request = new JSONRPCRequest($params);
        return $this->transport->sendRequest($request)
            ->then(function($response) use ($schema) {
                if ($schema !== null) {
                    // TODO: Implement schema validation
                }
                return $response;
            });
    }

    public function close(): void {
        if ($this->transport) {
            $this->transport->close();
        }
    }
}
