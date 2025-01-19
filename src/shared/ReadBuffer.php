<?php

namespace ModelContextProtocol\Shared;

use ModelContextProtocol\Types\JSONRPCMessage;

class ReadBuffer {
    private string $buffer = "";

    public function append($chunk) {
        $this->buffer .= $chunk;
    }

    public function readMessage(): JSONRPCMessage|null
    {
        $pos = strpos($this->buffer, "\n");
        if ($pos === false) {
            return null;
        }

        $line = substr($this->buffer, 0, $pos);
        $this->buffer = substr($this->buffer, $pos + 1);

        return $this->deserializeMessage($line);
    }

    public function clear() {
        $this->buffer = "";
    }

    private function deserializeMessage($line): JSONRPCMessage 
    {
        return JSONRPCMessage::fromJsonString($line);
    }
    
    public static function serializeMessage(JSONRPCMessage $message): string 
    {
        return json_encode($message, JSON_THROW_ON_ERROR) . "\n";
    }
}
