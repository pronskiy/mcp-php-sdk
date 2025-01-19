<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class JSONRPCError implements JSONRPCMessageInterface
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'A response to a request that indicates an error occurred.',
        'properties' => [
            'error' => [
                'properties' => [
                    'code' => [
                        'description' => 'The error type that occurred.',
                        'type' => 'integer',
                    ],
                    'data' => [
                        'description' => 'Additional information about the error. The value of this member is defined by the sender (e.g. detailed error information, nested errors etc.).',
                    ],
                    'message' => [
                        'description' => 'A short description of the error. The message SHOULD be limited to a concise single sentence.',
                        'type' => 'string',
                    ],
                ],
                'required' => [
                    'code',
                    'message',
                ],
                'type' => 'object',
            ],
            'id' => [
                '$ref' => '#/definitions/RequestId',
            ],
            'jsonrpc' => [
                'const' => '2.0',
                'type' => 'string',
            ],
        ],
        'required' => [
            'error',
            'id',
            'jsonrpc',
        ],
        'type' => 'object',
    ];

    /**
     * @var JSONRPCErrorError
     */
    private int $error;

    /**
     * @var mixed
     */
    private mixed $id;

    /**
     * @var string
     */
    private string $jsonrpc;

    /**
     * @param JSONRPCErrorError $error
     * @param mixed $id
     * @param string $jsonrpc
     */
    public function __construct(int $error, mixed $id, string $jsonrpc = self::JSONRPC_VERSION)
    {
        $this->error = $error;
        $this->id = $id;
        $this->jsonrpc = $jsonrpc;
    }

    /**
     * @return JSONRPCErrorError
     */
    public function getError() : JSONRPCErrorError
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getId() : mixed
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getJsonrpc() : string
    {
        return $this->jsonrpc;
    }

    /**
     * @param JSONRPCErrorError $error
     * @return self
     */
    public function withError(JSONRPCErrorError $error) : self
    {
        $clone = clone $this;
        $clone->error = $error;

        return $clone;
    }

    /**
     * @param mixed $id
     * @return self
     */
    public function withId(mixed $id) : self
    {
        $clone = clone $this;
        $clone->id = $id;

        return $clone;
    }

    /**
     * @param string $jsonrpc
     * @return self
     */
    public function withJsonrpc(string $jsonrpc) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($jsonrpc, static::$schema['properties']['jsonrpc']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->jsonrpc = $jsonrpc;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return JSONRPCError Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : JSONRPCError
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $error = JSONRPCErrorError::buildFromInput($input->{'error'}, validate: $validate);
        $id = $input->{'id'};
        $jsonrpc = $input->{'jsonrpc'};

        $obj = new self($error, $id, $jsonrpc);

        return $obj;
    }

    /**
     * Converts this object back to a simple array that can be JSON-serialized
     *
     * @return array Converted array
     */
    public function toJson() : array
    {
        $output = [];
        $output['error'] = ($this->error)->toJson();
        $output['id'] = $this->id;
        $output['jsonrpc'] = $this->jsonrpc;

        return $output;
    }

    /**
     * Validates an input array
     *
     * @param array|object $input Input data
     * @param bool $return Return instead of throwing errors
     * @return bool Validation result
     * @throws \InvalidArgumentException
     */
    public static function validateInput(array|object $input, bool $return = false) : bool
    {
        $validator = new \JsonSchema\Validator();
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        $validator->validate($input, static::$schema);

        if (!$validator->isValid() && !$return) {
            $errors = array_map(function(array $e): string {
                return $e["property"] . ": " . $e["message"];
            }, $validator->getErrors());
            throw new \InvalidArgumentException(join(", ", $errors));
        }

        return $validator->isValid();
    }

    public function __clone()
    {
        $this->error = clone $this->error;
    }
}

