<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class CallToolResult
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'The server\'s response to a tool call.

Any errors that originate from the tool SHOULD be reported inside the result
object, with `isError` set to true, _not_ as an MCP protocol-level error
response. Otherwise, the LLM would not be able to see that an error occurred
and self-correct.

However, any errors in _finding_ the tool, an error indicating that the
server does not support tool calls, or any other exceptional conditions,
should be reported as an MCP error response.',
        'properties' => [
            '_meta' => [
                'additionalProperties' => [
                    
                ],
                'description' => 'This result property is reserved by the protocol to allow clients and servers to attach additional metadata to their responses.',
                'type' => 'object',
            ],
            'content' => [
                'items' => [
                    'anyOf' => [
                        [
                            '$ref' => '#/definitions/TextContent',
                        ],
                        [
                            '$ref' => '#/definitions/ImageContent',
                        ],
                        [
                            '$ref' => '#/definitions/EmbeddedResource',
                        ],
                    ],
                ],
                'type' => 'array',
            ],
            'isError' => [
                'description' => 'Whether the tool call ended in an error.

If not set, this is assumed to be false (the call was successful).',
                'type' => 'boolean',
            ],
        ],
        'required' => [
            'content',
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
     * @var mixed[]
     */
    private array $content;

    /**
     * Whether the tool call ended in an error.
     *
     * If not set, this is assumed to be false (the call was successful).
     *
     * @var bool|null
     */
    private ?bool $isError = null;

    /**
     * @param mixed[] $content
     */
    public function __construct(array $content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed[]|null
     */
    public function getMeta() : ?array
    {
        return $this->Meta ?? null;
    }

    /**
     * @return mixed[]
     */
    public function getContent() : array
    {
        return $this->content;
    }

    /**
     * @return bool|null
     */
    public function getIsError() : ?bool
    {
        return $this->isError ?? null;
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
     * @param mixed[] $content
     * @return self
     */
    public function withContent(array $content) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($content, static::$schema['properties']['content']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->content = $content;

        return $clone;
    }

    /**
     * @param bool $isError
     * @return self
     */
    public function withIsError(bool $isError) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($isError, static::$schema['properties']['isError']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->isError = $isError;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutIsError() : self
    {
        $clone = clone $this;
        unset($clone->isError);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return CallToolResult Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : CallToolResult
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $Meta = null;
        if (isset($input->{'_meta'})) {
            $Meta = (array)$input->{'_meta'};
        }
        $content = $input->{'content'};
        $isError = null;
        if (isset($input->{'isError'})) {
            $isError = (bool)($input->{'isError'});
        }

        $obj = new self($content);
        $obj->Meta = $Meta;
        $obj->isError = $isError;
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
        $output['content'] = $this->content;
        if (isset($this->isError)) {
            $output['isError'] = $this->isError;
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

