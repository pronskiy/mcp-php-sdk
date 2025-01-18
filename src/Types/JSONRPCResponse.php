<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class JSONRPCResponse
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'A successful (non-error) response to a request.',
        'properties' => [
            'id' => [
                '$ref' => '#/definitions/RequestId',
            ],
            'jsonrpc' => [
                'const' => '2.0',
                'type' => 'string',
            ],
            'result' => [
                '$ref' => '#/definitions/Result',
            ],
        ],
        'required' => [
            'id',
            'jsonrpc',
            'result',
        ],
        'type' => 'object',
    ];

    /**
     * @var mixed
     */
    private mixed $id;

    /**
     * @var string
     */
    private string $jsonrpc;

    /**
     * @var mixed
     */
    private mixed $result;

    /**
     * @param mixed $id
     * @param string $jsonrpc
     * @param mixed $result
     */
    public function __construct(mixed $id, string $jsonrpc, mixed $result)
    {
        $this->id = $id;
        $this->jsonrpc = $jsonrpc;
        $this->result = $result;
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
     * @return mixed
     */
    public function getResult() : mixed
    {
        return $this->result;
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
     * @param mixed $result
     * @return self
     */
    public function withResult(mixed $result) : self
    {
        $clone = clone $this;
        $clone->result = $result;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return JSONRPCResponse Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : JSONRPCResponse
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $id = $input->{'id'};
        $jsonrpc = $input->{'jsonrpc'};
        $result = $input->{'result'};

        $obj = new self($id, $jsonrpc, $result);

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
        $output['id'] = $this->id;
        $output['jsonrpc'] = $this->jsonrpc;
        $output['result'] = $this->result;

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
    }
}

