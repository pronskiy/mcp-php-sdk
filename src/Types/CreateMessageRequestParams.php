<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class CreateMessageRequestParams
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
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
    ];

    /**
     * A request to include context from one or more MCP servers (including the caller), to be attached to the prompt. The client MAY ignore this request.
     *
     * @var CreateMessageRequestParamsIncludeContext|null
     */
    private ?CreateMessageRequestParamsIncludeContext $includeContext = null;

    /**
     * The maximum number of tokens to sample, as requested by the server. The client MAY choose to sample fewer tokens than requested.
     *
     * @var int
     */
    private int $maxTokens;

    /**
     * @var mixed[]
     */
    private array $messages;

    /**
     * Optional metadata to pass through to the LLM provider. The format of this metadata is provider-specific.
     *
     * @var CreateMessageRequestParamsMetadata|null
     */
    private ?CreateMessageRequestParamsMetadata $metadata = null;

    /**
     * The server's preferences for which model to select. The client MAY ignore these preferences.
     *
     * @var mixed|null
     */
    private mixed $modelPreferences = null;

    /**
     * @var string[]|null
     */
    private ?array $stopSequences = null;

    /**
     * An optional system prompt the server wants to use for sampling. The client MAY modify or omit this prompt.
     *
     * @var string|null
     */
    private ?string $systemPrompt = null;

    /**
     * @var int|float|null
     */
    private int|float|null $temperature = null;

    /**
     * @param int $maxTokens
     * @param mixed[] $messages
     */
    public function __construct(int $maxTokens, array $messages)
    {
        $this->maxTokens = $maxTokens;
        $this->messages = $messages;
    }

    /**
     * @return CreateMessageRequestParamsIncludeContext|null
     */
    public function getIncludeContext() : ?CreateMessageRequestParamsIncludeContext
    {
        return $this->includeContext ?? null;
    }

    /**
     * @return int
     */
    public function getMaxTokens() : int
    {
        return $this->maxTokens;
    }

    /**
     * @return mixed[]
     */
    public function getMessages() : array
    {
        return $this->messages;
    }

    /**
     * @return CreateMessageRequestParamsMetadata|null
     */
    public function getMetadata() : ?CreateMessageRequestParamsMetadata
    {
        return $this->metadata ?? null;
    }

    /**
     * @return mixed|null
     */
    public function getModelPreferences() : mixed
    {
        return $this->modelPreferences;
    }

    /**
     * @return string[]|null
     */
    public function getStopSequences() : ?array
    {
        return $this->stopSequences ?? null;
    }

    /**
     * @return string|null
     */
    public function getSystemPrompt() : ?string
    {
        return $this->systemPrompt ?? null;
    }

    /**
     * @return int|float|null
     */
    public function getTemperature() : int|float|null
    {
        return $this->temperature;
    }

    /**
     * @param CreateMessageRequestParamsIncludeContext $includeContext
     * @return self
     */
    public function withIncludeContext(CreateMessageRequestParamsIncludeContext $includeContext) : self
    {
        $clone = clone $this;
        $clone->includeContext = $includeContext;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutIncludeContext() : self
    {
        $clone = clone $this;
        unset($clone->includeContext);

        return $clone;
    }

    /**
     * @param int $maxTokens
     * @return self
     */
    public function withMaxTokens(int $maxTokens) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($maxTokens, static::$schema['properties']['maxTokens']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->maxTokens = $maxTokens;

        return $clone;
    }

    /**
     * @param mixed[] $messages
     * @return self
     */
    public function withMessages(array $messages) : self
    {
        $clone = clone $this;
        $clone->messages = $messages;

        return $clone;
    }

    /**
     * @param CreateMessageRequestParamsMetadata $metadata
     * @return self
     */
    public function withMetadata(CreateMessageRequestParamsMetadata $metadata) : self
    {
        $clone = clone $this;
        $clone->metadata = $metadata;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutMetadata() : self
    {
        $clone = clone $this;
        unset($clone->metadata);

        return $clone;
    }

    /**
     * @param mixed $modelPreferences
     * @return self
     */
    public function withModelPreferences(mixed $modelPreferences) : self
    {
        $clone = clone $this;
        $clone->modelPreferences = $modelPreferences;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutModelPreferences() : self
    {
        $clone = clone $this;
        unset($clone->modelPreferences);

        return $clone;
    }

    /**
     * @param string[] $stopSequences
     * @return self
     */
    public function withStopSequences(array $stopSequences) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($stopSequences, static::$schema['properties']['stopSequences']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->stopSequences = $stopSequences;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutStopSequences() : self
    {
        $clone = clone $this;
        unset($clone->stopSequences);

        return $clone;
    }

    /**
     * @param string $systemPrompt
     * @return self
     */
    public function withSystemPrompt(string $systemPrompt) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($systemPrompt, static::$schema['properties']['systemPrompt']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->systemPrompt = $systemPrompt;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutSystemPrompt() : self
    {
        $clone = clone $this;
        unset($clone->systemPrompt);

        return $clone;
    }

    /**
     * @param int|float $temperature
     * @return self
     */
    public function withTemperature(int|float $temperature) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($temperature, static::$schema['properties']['temperature']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->temperature = $temperature;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutTemperature() : self
    {
        $clone = clone $this;
        unset($clone->temperature);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return CreateMessageRequestParams Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : CreateMessageRequestParams
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $includeContext = null;
        if (isset($input->{'includeContext'})) {
            $includeContext = CreateMessageRequestParamsIncludeContext::from($input->{'includeContext'});
        }
        $maxTokens = (int)($input->{'maxTokens'});
        $messages = array_map(fn(mixed $i): mixed => $i, $input->{'messages'});
        $metadata = null;
        if (isset($input->{'metadata'})) {
            $metadata = CreateMessageRequestParamsMetadata::buildFromInput($input->{'metadata'}, validate: $validate);
        }
        $modelPreferences = null;
        if (isset($input->{'modelPreferences'})) {
            $modelPreferences = $input->{'modelPreferences'};
        }
        $stopSequences = null;
        if (isset($input->{'stopSequences'})) {
            $stopSequences = $input->{'stopSequences'};
        }
        $systemPrompt = null;
        if (isset($input->{'systemPrompt'})) {
            $systemPrompt = $input->{'systemPrompt'};
        }
        $temperature = null;
        if (isset($input->{'temperature'})) {
            $temperature = str_contains((string)($input->{'temperature'}), '.') ? (float)($input->{'temperature'}) : (int)($input->{'temperature'});
        }

        $obj = new self($maxTokens, $messages);
        $obj->includeContext = $includeContext;
        $obj->metadata = $metadata;
        $obj->modelPreferences = $modelPreferences;
        $obj->stopSequences = $stopSequences;
        $obj->systemPrompt = $systemPrompt;
        $obj->temperature = $temperature;
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
        if (isset($this->includeContext)) {
            $output['includeContext'] = ($this->includeContext)->value;
        }
        $output['maxTokens'] = $this->maxTokens;
        $output['messages'] = array_map(fn(mixed $i): mixed => $i, $this->messages);
        if (isset($this->metadata)) {
            $output['metadata'] = ($this->metadata)->toJson();
        }
        if (isset($this->modelPreferences)) {
            $output['modelPreferences'] = $this->modelPreferences;
        }
        if (isset($this->stopSequences)) {
            $output['stopSequences'] = $this->stopSequences;
        }
        if (isset($this->systemPrompt)) {
            $output['systemPrompt'] = $this->systemPrompt;
        }
        if (isset($this->temperature)) {
            $output['temperature'] = $this->temperature;
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
        if (isset($this->metadata)) {
            $this->metadata = clone $this->metadata;
        }
    }
}

