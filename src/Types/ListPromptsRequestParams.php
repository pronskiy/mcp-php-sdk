<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ListPromptsRequestParams
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'properties' => [
            'cursor' => [
                'description' => 'An opaque token representing the current pagination position.
If provided, the server should return results starting after this cursor.',
                'type' => 'string',
            ],
        ],
        'type' => 'object',
    ];

    /**
     * An opaque token representing the current pagination position.
     * If provided, the server should return results starting after this cursor.
     *
     * @var string|null
     */
    private ?string $cursor = null;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return string|null
     */
    public function getCursor() : ?string
    {
        return $this->cursor ?? null;
    }

    /**
     * @param string $cursor
     * @return self
     */
    public function withCursor(string $cursor) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($cursor, static::$schema['properties']['cursor']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->cursor = $cursor;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutCursor() : self
    {
        $clone = clone $this;
        unset($clone->cursor);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return ListPromptsRequestParams Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ListPromptsRequestParams
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $cursor = null;
        if (isset($input->{'cursor'})) {
            $cursor = $input->{'cursor'};
        }

        $obj = new self();
        $obj->cursor = $cursor;
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
        if (isset($this->cursor)) {
            $output['cursor'] = $this->cursor;
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

