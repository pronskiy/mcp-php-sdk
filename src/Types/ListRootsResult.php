<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ListRootsResult
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'The client\'s response to a roots/list request from the server.
This result contains an array of Root objects, each representing a root directory
or file that the server can operate on.',
        'properties' => [
            '_meta' => [
                'additionalProperties' => [
                    
                ],
                'description' => 'This result property is reserved by the protocol to allow clients and servers to attach additional metadata to their responses.',
                'type' => 'object',
            ],
            'roots' => [
                'items' => [
                    '$ref' => '#/definitions/Root',
                ],
                'type' => 'array',
            ],
        ],
        'required' => [
            'roots',
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
    private array $roots;

    /**
     * @param mixed[] $roots
     */
    public function __construct(array $roots)
    {
        $this->roots = $roots;
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
    public function getRoots() : array
    {
        return $this->roots;
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
     * @param mixed[] $roots
     * @return self
     */
    public function withRoots(array $roots) : self
    {
        $clone = clone $this;
        $clone->roots = $roots;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return ListRootsResult Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ListRootsResult
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $Meta = null;
        if (isset($input->{'_meta'})) {
            $Meta = (array)$input->{'_meta'};
        }
        $roots = array_map(fn(mixed $i): mixed => $i, $input->{'roots'});

        $obj = new self($roots);
        $obj->Meta = $Meta;
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
        $output['roots'] = array_map(fn(mixed $i): mixed => $i, $this->roots);

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

