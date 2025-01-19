<?php

use ModelContextProtocol\Types\JSONRPCMessage;
use ModelContextProtocol\Shared\ReadBuffer;

$testMessage = new JSONRPCMessage(
    jsonrpc: "2.0",
    method: "foobar",
);

test('example', function () {
    expect(true)->toBeTrue();
});

test('should have no messages after initialization', function () {
    $readBuffer = new ReadBuffer();
    expect($readBuffer->readMessage())->toBeNull();
});

test("should only yield a message after a newline", function () use ($testMessage) {
    $readBuffer = new ReadBuffer();

    $readBuffer->append(json_encode($testMessage));
    expect($readBuffer->readMessage())->toBeNull();

    $readBuffer->append("\n");
    
    expect($readBuffer->readMessage())->toEqual($testMessage);
    expect($readBuffer->readMessage())->toBeNull();
});

test("should be reusable after clearing", function () use ($testMessage) {
    $readBuffer = new ReadBuffer();

    $readBuffer->append(json_encode("foobar"));
    $readBuffer->clear();
    expect($readBuffer->readMessage())->toBeNull();

    $readBuffer->append(json_encode($testMessage));
    $readBuffer->append("\n");
    expect($readBuffer->readMessage())->toEqual($testMessage);
});
