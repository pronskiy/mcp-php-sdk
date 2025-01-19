<?php

namespace ModelContextProtocol\Shared;

use ModelContextProtocol\Types\JSONRPCMessage;

interface Transport 
{
    public function start();
    public function send(JSONRPCMessage $message);
    public function close();
    public function setOnClose(callable $callback);
    public function setOnError(callable $callback);
    public function setOnMessage(callable $callback);
}
