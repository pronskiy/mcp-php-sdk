<?php

namespace ModelContextProtocol\Types;

use ModelContextProtocol\Types\Result;
use Square\Pjson\Json;
use Square\Pjson\JsonSerialize;

class JSONRPCMessage 
{
    use JsonSerialize;

    #[Json]
    public string $jsonrpc;

    #[Json]
    public ?string $method;

    #[Json]
    public mixed $id;

    #[Json]
    public ?\ModelContextProtocol\Types\Result $result;

    public function __construct(
        string           $jsonrpc = JSONRPCMessageInterface::JSONRPC_VERSION,
        ?string           $method = '',
        mixed       $id = '',
        ?Result $result = null,
    ) {
        $this->jsonrpc = $jsonrpc;
        $this->method = $method;
        $this->id = $id;
        $this->result = $result;
    }

    public static function buildFromInput(array|object $input, bool $validate = true) : JSONRPCMessage
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
//        if ($validate) {
//            static::validateInput($input);
//        }

        $jsonrpc = $input->jsonrpc ?? null;
        $method = $input->method ?? null;
        $id = $input->id ?? null;
        $result = $input->result ?? null;

        $obj = new self($jsonrpc, $method, $id, $result);
        
        return $obj;
    }
}
