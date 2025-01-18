<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ListPromptsResult
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'The server\'s response to a prompts/list request from the client.',
        'properties' => [
            '_meta' => [
                'additionalProperties' => [
                    
                ],
                'description' => 'This result property is reserved by the protocol to allow clients and servers to attach additional metadata to their responses.',
                'type' => 'object',
            ],
            'nextCursor' => [
                'description' => 'An opaque token representing the pagination position after the last returned result.
If present, there may be more results available.',
                'type' => 'string',
            ],
            'prompts' => [
                'items' => [
                    '$ref' => '#/definitions/Prompt',
                ],
                'type' => 'array',
            ],
        ],
        'required' => [
            'prompts',
        ],
        'type' => 'object',
    ];

    /**
     * This result property is reserved by the protocol to allow clients and servers to attach additional metadata to their responses.
     *
     * @var mixed[]|null
     */
    private ?array $Meta = null;

    /**
     * An opaque token representing the pagination position after the last returned result.
     * If present, there may be more results available.
     *
     * @var string|null
     */
    private ?string $nextCursor = null;

    /**
     * @var mixed[]
     */
    private array $prompts;

    /**
     * @param mixed[] $prompts
     */
    public function __construct(array $prompts)
    {
        $this->prompts = $prompts;
    }

    /**
     * @return mixed[]|null
     */
    public function getMeta() : ?array
    {
        return $this->Meta ?? null;
    }

    /**
     * @return string|null
     */
    public function getNextCursor() : ?string
    {
        return $this->nextCursor ?? null;
    }

    /**
     * @return mixed[]
     */
    public function getPrompts() : array
    {
        return $this->prompts;
    }

    /**
     * @param mixed[] $Meta
     * @return self
     */
    public function withMeta(array $Meta) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($Meta, static::$schema['properties']['_meta']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->Meta = $Meta;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutMeta() : self
    {
        $clone = clone $this;
        unset($clone->Meta);

        return $clone;
    }

    /**
     * @param string $nextCursor
     * @return self
     */
    public function withNextCursor(string $nextCursor) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($nextCursor, static::$schema['properties']['nextCursor']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->nextCursor = $nextCursor;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutNextCursor() : self
    {
        $clone = clone $this;
        unset($clone->nextCursor);

        return $clone;
    }

    /**
     * @param mixed[] $prompts
     * @return self
     */
    public function withPrompts(array $prompts) : self
    {
        $clone = clone $this;
        $clone->prompts = $prompts;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return ListPromptsResult Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ListPromptsResult
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $Meta = null;
        if (isset($input->{'_meta'})) {
            $Meta = (array)$input->{'_meta'};
        }
        $nextCursor = null;
        if (isset($input->{'nextCursor'})) {
            $nextCursor = $input->{'nextCursor'};
        }
        $prompts = array_map(fn(mixed $i): mixed => $i, $input->{'prompts'});

        $obj = new self($prompts);
        $obj->Meta = $Meta;
        $obj->nextCursor = $nextCursor;
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
        if (isset($this->Meta)) {
            $output['_meta'] = $this->Meta;
        }
        if (isset($this->nextCursor)) {
            $output['nextCursor'] = $this->nextCursor;
        }
        $output['prompts'] = array_map(fn(mixed $i): mixed => $i, $this->prompts);

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

