<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class CompleteRequest
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'A request from the client to the server, to ask for completion options.',
        'properties' => [
            'method' => [
                'const' => 'completion/complete',
                'type' => 'string',
            ],
            'params' => [
                'properties' => [
                    'argument' => [
                        'description' => 'The argument\'s information',
                        'properties' => [
                            'name' => [
                                'description' => 'The name of the argument',
                                'type' => 'string',
                            ],
                            'value' => [
                                'description' => 'The value of the argument to use for completion matching.',
                                'type' => 'string',
                            ],
                        ],
                        'required' => [
                            'name',
                            'value',
                        ],
                        'type' => 'object',
                    ],
                    'ref' => [
                        'anyOf' => [
                            [
                                '$ref' => '#/definitions/PromptReference',
                            ],
                            [
                                '$ref' => '#/definitions/ResourceReference',
                            ],
                        ],
                    ],
                ],
                'required' => [
                    'argument',
                    'ref',
                ],
                'type' => 'object',
            ],
        ],
        'required' => [
            'method',
            'params',
        ],
        'type' => 'object',
    ];

    /**
     * @var string
     */
    private string $method;

    /**
     * @var CompleteRequestParams
     */
    private CompleteRequestParams $params;

    /**
     * @param string $method
     * @param CompleteRequestParams $params
     */
    public function __construct(string $method, CompleteRequestParams $params)
    {
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * @return CompleteRequestParams
     */
    public function getParams() : CompleteRequestParams
    {
        return $this->params;
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
     * @param CompleteRequestParams $params
     * @return self
     */
    public function withParams(CompleteRequestParams $params) : self
    {
        $clone = clone $this;
        $clone->params = $params;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return CompleteRequest Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : CompleteRequest
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $method = $input->{'method'};
        $params = CompleteRequestParams::buildFromInput($input->{'params'}, validate: $validate);

        $obj = new self($method, $params);

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
        $output['params'] = ($this->params)->toJson();

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
        $this->params = clone $this->params;
    }
}

