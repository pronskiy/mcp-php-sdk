<?php

namespace ModelContextProtocol\Client;

use ModelContextProtocol\Types\JSONRPCRequest;
use ModelContextProtocol\Types\JSONRPCResponse;

interface Transport {
    public function connect(): void;
    public function sendRequest(JSONRPCRequest $request): \React\Promise\PromiseInterface;
    public function close(): void;
}
