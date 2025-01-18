<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class CreateMessageRequest
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'A request from the server to sample an LLM via the client. The client has full discretion over which model to select. The client should also inform the user before beginning sampling, to allow them to inspect the request (human in the loop) and decide whether to approve it.',
        'properties' => [
            'method' => [
                'const' => 'sampling/createMessage',
                'type' => 'string',
            ],
            'params' => [
                'properties' => [
                    'includeContext' => [
                        'description' => 'A request to include context from one or more MCP servers (including the caller), to be attached to the prompt. The client MAY ignore this request.',
                        'enum' => [
                            'allServers',
                            'none',
                            'thisServer',
                        ],
                        'type' => 'string',
                    ],
                    'maxTokens' => [
                        'description' => 'The maximum number of tokens to sample, as requested by the server. The client MAY choose to sample fewer tokens than requested.',
                        'type' => 'integer',
                    ],
                    'messages' => [
                        'items' => [
                            '$ref' => '#/definitions/SamplingMessage',
                        ],
                        'type' => 'array',
                    ],
                    'metadata' => [
                        'additionalProperties' => true,
                        'description' => 'Optional metadata to pass through to the LLM provider. The format of this metadata is provider-specific.',
                        'properties' => [
                            
                        ],
                        'type' => 'object',
                    ],
                    'modelPreferences' => [
                        '$ref' => '#/definitions/ModelPreferences',
                        'description' => 'The server\'s preferences for which model to select. The client MAY ignore these preferences.',
                    ],
                    'stopSequences' => [
                        'items' => [
                            'type' => 'string',
                        ],
                        'type' => 'array',
                    ],
                    'systemPrompt' => [
                        'description' => 'An optional system prompt the server wants to use for sampling. The client MAY modify or omit this prompt.',
                        'type' => 'string',
                    ],
                    'temperature' => [
                        'type' => 'number',
                    ],
                ],
                'required' => [
                    'maxTokens',
                    'messages',
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
     * @var CreateMessageRequestParams
     */
    private CreateMessageRequestParams $params;

    /**
     * @param string $method
     * @param CreateMessageRequestParams $params
     */
    public function __construct(string $method, CreateMessageRequestParams $params)
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
     * @return CreateMessageRequestParams
     */
    public function getParams() : CreateMessageRequestParams
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
     * @param CreateMessageRequestParams $params
     * @return self
     */
    public function withParams(CreateMessageRequestParams $params) : self
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
     * @return CreateMessageRequest Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : CreateMessageRequest
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $method = $input->{'method'};
        $params = CreateMessageRequestParams::buildFromInput($input->{'params'}, validate: $validate);

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

