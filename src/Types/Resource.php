<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class Resource
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'A known resource that the server is capable of reading.',
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
            'description' => [
                'description' => 'A description of what this resource represents.

This can be used by clients to improve the LLM\'s understanding of available resources. It can be thought of like a "hint" to the model.',
                'type' => 'string',
            ],
            'mimeType' => [
                'description' => 'The MIME type of this resource, if known.',
                'type' => 'string',
            ],
            'name' => [
                'description' => 'A human-readable name for this resource.

This can be used by clients to populate UI elements.',
                'type' => 'string',
            ],
            'uri' => [
                'description' => 'The URI of this resource.',
                'format' => 'uri',
                'type' => 'string',
            ],
        ],
        'required' => [
            'name',
            'uri',
        ],
        'type' => 'object',
    ];

    /**
     * @var ResourceAnnotations|null
     */
    private ?ResourceAnnotations $annotations = null;

    /**
     * A description of what this resource represents.
     *
     * This can be used by clients to improve the LLM's understanding of available resources. It can be thought of like a "hint" to the model.
     *
     * @var string|null
     */
    private ?string $description = null;

    /**
     * The MIME type of this resource, if known.
     *
     * @var string|null
     */
    private ?string $mimeType = null;

    /**
     * A human-readable name for this resource.
     *
     * This can be used by clients to populate UI elements.
     *
     * @var string
     */
    private string $name;

    /**
     * The URI of this resource.
     *
     * @var string
     */
    private string $uri;

    /**
     * @param string $name
     * @param string $uri
     */
    public function __construct(string $name, string $uri)
    {
        $this->name = $name;
        $this->uri = $uri;
    }

    /**
     * @return ResourceAnnotations|null
     */
    public function getAnnotations() : ?ResourceAnnotations
    {
        return $this->annotations ?? null;
    }

    /**
     * @return string|null
     */
    public function getDescription() : ?string
    {
        return $this->description ?? null;
    }

    /**
     * @return string|null
     */
    public function getMimeType() : ?string
    {
        return $this->mimeType ?? null;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUri() : string
    {
        return $this->uri;
    }

    /**
     * @param ResourceAnnotations $annotations
     * @return self
     */
    public function withAnnotations(ResourceAnnotations $annotations) : self
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
     * @param string $description
     * @return self
     */
    public function withDescription(string $description) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($description, static::$schema['properties']['description']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->description = $description;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutDescription() : self
    {
        $clone = clone $this;
        unset($clone->description);

        return $clone;
    }

    /**
     * @param string $mimeType
     * @return self
     */
    public function withMimeType(string $mimeType) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($mimeType, static::$schema['properties']['mimeType']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->mimeType = $mimeType;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutMimeType() : self
    {
        $clone = clone $this;
        unset($clone->mimeType);

        return $clone;
    }

    /**
     * @param string $name
     * @return self
     */
    public function withName(string $name) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($name, static::$schema['properties']['name']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->name = $name;

        return $clone;
    }

    /**
     * @param string $uri
     * @return self
     */
    public function withUri(string $uri) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($uri, static::$schema['properties']['uri']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->uri = $uri;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return Resource Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : Resource
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $annotations = null;
        if (isset($input->{'annotations'})) {
            $annotations = ResourceAnnotations::buildFromInput($input->{'annotations'}, validate: $validate);
        }
        $description = null;
        if (isset($input->{'description'})) {
            $description = $input->{'description'};
        }
        $mimeType = null;
        if (isset($input->{'mimeType'})) {
            $mimeType = $input->{'mimeType'};
        }
        $name = $input->{'name'};
        $uri = $input->{'uri'};

        $obj = new self($name, $uri);
        $obj->annotations = $annotations;
        $obj->description = $description;
        $obj->mimeType = $mimeType;
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
        if (isset($this->description)) {
            $output['description'] = $this->description;
        }
        if (isset($this->mimeType)) {
            $output['mimeType'] = $this->mimeType;
        }
        $output['name'] = $this->name;
        $output['uri'] = $this->uri;

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
    }
}

