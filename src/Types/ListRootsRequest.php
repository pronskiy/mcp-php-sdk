<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ListRootsRequest
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'Sent from the server to request a list of root URIs from the client. Roots allow
servers to ask for specific directories or files to operate on. A common example
for roots is providing a set of repositories or directories a server should operate
on.

This request is typically used when the server needs to understand the file system
structure or access specific locations that the client has permission to read from.',
        'properties' => [
            'method' => [
                'const' => 'roots/list',
                'type' => 'string',
            ],
            'params' => [
                'additionalProperties' => [
                    
                ],
                'properties' => [
                    '_meta' => [
                        'properties' => [
                            'progressToken' => [
                                '$ref' => '#/definitions/ProgressToken',
                                'description' => 'If specified, the caller is requesting out-of-band progress notifications for this request (as represented by notifications/progress). The value of this parameter is an opaque token that will be attached to any subsequent notifications. The receiver is not obligated to provide these notifications.',
                            ],
                        ],
                        'type' => 'object',
                    ],
                ],
                'type' => 'object',
            ],
        ],
        'required' => [
            'method',
        ],
        'type' => 'object',
    ];

    /**
     * @var string
     */
    private string $method;

    /**
     * @var mixed[]|null
     */
    private ?array $params = null;

    /**
     * @param string $method
     */
    public function __construct(string $method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * @return mixed[]|null
     */
    public function getParams() : ?array
    {
        return $this->params ?? null;
    }

    /**
     * @param string $method
     * @return self
     */
    public function withMethod(string $method) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($method, static::$schema['properties']['method']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    /**
     * @param mixed[] $params
     * @return self
     */
    public function withParams(array $params) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($params, static::$schema['properties']['params']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->params = $params;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutParams() : self
    {
        $clone = clone $this;
        unset($clone->params);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return ListRootsRequest Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ListRootsRequest
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $method = $input->{'method'};
        $params = null;
        if (isset($input->{'params'})) {
            $params = (array)$input->{'params'};
        }

        $obj = new self($method);
        $obj->params = $params;
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
        $output['method'] = $this->method;
        if (isset($this->params)) {
            $output['params'] = $this->params;
        }

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

