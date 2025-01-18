<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class EmbeddedResource
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'The contents of a resource, embedded into a prompt or tool call result.

It is up to the client how best to render embedded resources for the benefit
of the LLM and/or the user.',
        'properties' => [
            'annotations' => [
                'properties' => [
                    'audience' => [
                        'description' => 'Describes who the intended customer of this object or data is.

It can include multiple entries to indicate content useful for multiple audiences (e.g., `["user", "assistant"]`).',
                        'items' => [
                            '$ref' => '#/definitions/Role',
                        ],
                        'type' => 'array',
                    ],
                    'priority' => [
                        'description' => 'Describes how important this data is for operating the server.

A value of 1 means "most important," and indicates that the data is
effectively required, while 0 means "least important," and indicates that
the data is entirely optional.',
                        'maximum' => 1,
                        'minimum' => 0,
                        'type' => 'number',
                    ],
                ],
                'type' => 'object',
            ],
            'resource' => [
                'anyOf' => [
                    [
                        '$ref' => '#/definitions/TextResourceContents',
                    ],
                    [
                        '$ref' => '#/definitions/BlobResourceContents',
                    ],
                ],
            ],
            'type' => [
                'const' => 'resource',
                'type' => 'string',
            ],
        ],
        'required' => [
            'resource',
            'type',
        ],
        'type' => 'object',
    ];

    /**
     * @var EmbeddedResourceAnnotations|null
     */
    private ?EmbeddedResourceAnnotations $annotations = null;

    /**
     * @var mixed
     */
    private mixed $resource;

    /**
     * @var string
     */
    private string $type;

    /**
     * @param mixed $resource
     * @param string $type
     */
    public function __construct(mixed $resource, string $type)
    {
        $this->resource = $resource;
        $this->type = $type;
    }

    /**
     * @return EmbeddedResourceAnnotations|null
     */
    public function getAnnotations() : ?EmbeddedResourceAnnotations
    {
        return $this->annotations ?? null;
    }

    /**
     * @return mixed
     */
    public function getResource() : mixed
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param EmbeddedResourceAnnotations $annotations
     * @return self
     */
    public function withAnnotations(EmbeddedResourceAnnotations $annotations) : self
    {
        $clone = clone $this;
        $clone->annotations = $annotations;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutAnnotations() : self
    {
        $clone = clone $this;
        unset($clone->annotations);

        return $clone;
    }

    /**
     * @param mixed $resource
     * @return self
     */
    public function withResource(mixed $resource) : self
    {
        $clone = clone $this;
        $clone->resource = $resource;

        return $clone;
    }

    /**
     * @param string $type
     * @return self
     */
    public function withType(string $type) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($type, static::$schema['properties']['type']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->type = $type;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return EmbeddedResource Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : EmbeddedResource
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $annotations = null;
        if (isset($input->{'annotations'})) {
            $annotations = EmbeddedResourceAnnotations::buildFromInput($input->{'annotations'}, validate: $validate);
        }
        $resource = match (true) {
            true => $input->{'resource'},
        };
        $type = $input->{'type'};

        $obj = new self($resource, $type);
        $obj->annotations = $annotations;
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
        if (isset($this->annotations)) {
            $output['annotations'] = ($this->annotations)->toJson();
        }
        $output['resource'] = match (true) {
            true => $this->resource,
        };
        $output['type'] = $this->type;

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
        if (isset($this->annotations)) {
            $this->annotations = clone $this->annotations;
        }
        $this->resource = match (true) {
            true => $this->resource,
        };
    }
}

