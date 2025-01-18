<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ListResourceTemplatesResult
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'The server\'s response to a resources/templates/list request from the client.',
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
            'resourceTemplates' => [
                'items' => [
                    '$ref' => '#/definitions/ResourceTemplate',
                ],
                'type' => 'array',
            ],
        ],
        'required' => [
            'resourceTemplates',
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
    private array $resourceTemplates;

    /**
     * @param mixed[] $resourceTemplates
     */
    public function __construct(array $resourceTemplates)
    {
        $this->resourceTemplates = $resourceTemplates;
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
    public function getResourceTemplates() : array
    {
        return $this->resourceTemplates;
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
     * @param mixed[] $resourceTemplates
     * @return self
     */
    public function withResourceTemplates(array $resourceTemplates) : self
    {
        $clone = clone $this;
        $clone->resourceTemplates = $resourceTemplates;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return ListResourceTemplatesResult Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ListResourceTemplatesResult
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
        $resourceTemplates = array_map(fn(mixed $i): mixed => $i, $input->{'resourceTemplates'});

        $obj = new self($resourceTemplates);
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
        $output['resourceTemplates'] = array_map(fn(mixed $i): mixed => $i, $this->resourceTemplates);

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

