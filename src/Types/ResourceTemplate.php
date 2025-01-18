<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ResourceTemplate
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'A template description for resources available on the server.',
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
                'description' => 'A description of what this template is for.

This can be used by clients to improve the LLM\'s understanding of available resources. It can be thought of like a "hint" to the model.',
                'type' => 'string',
            ],
            'mimeType' => [
                'description' => 'The MIME type for all resources that match this template. This should only be included if all resources matching this template have the same type.',
                'type' => 'string',
            ],
            'name' => [
                'description' => 'A human-readable name for the type of resource this template refers to.

This can be used by clients to populate UI elements.',
                'type' => 'string',
            ],
            'uriTemplate' => [
                'description' => 'A URI template (according to RFC 6570) that can be used to construct resource URIs.',
                'format' => 'uri-template',
                'type' => 'string',
            ],
        ],
        'required' => [
            'name',
            'uriTemplate',
        ],
        'type' => 'object',
    ];

    /**
     * @var ResourceTemplateAnnotations|null
     */
    private ?ResourceTemplateAnnotations $annotations = null;

    /**
     * A description of what this template is for.
     *
     * This can be used by clients to improve the LLM's understanding of available resources. It can be thought of like a "hint" to the model.
     *
     * @var string|null
     */
    private ?string $description = null;

    /**
     * The MIME type for all resources that match this template. This should only be included if all resources matching this template have the same type.
     *
     * @var string|null
     */
    private ?string $mimeType = null;

    /**
     * A human-readable name for the type of resource this template refers to.
     *
     * This can be used by clients to populate UI elements.
     *
     * @var string
     */
    private string $name;

    /**
     * A URI template (according to RFC 6570) that can be used to construct resource URIs.
     *
     * @var string
     */
    private string $uriTemplate;

    /**
     * @param string $name
     * @param string $uriTemplate
     */
    public function __construct(string $name, string $uriTemplate)
    {
        $this->name = $name;
        $this->uriTemplate = $uriTemplate;
    }

    /**
     * @return ResourceTemplateAnnotations|null
     */
    public function getAnnotations() : ?ResourceTemplateAnnotations
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
    public function getUriTemplate() : string
    {
        return $this->uriTemplate;
    }

    /**
     * @param ResourceTemplateAnnotations $annotations
     * @return self
     */
    public function withAnnotations(ResourceTemplateAnnotations $annotations) : self
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
     * @param string $uriTemplate
     * @return self
     */
    public function withUriTemplate(string $uriTemplate) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($uriTemplate, static::$schema['properties']['uriTemplate']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->uriTemplate = $uriTemplate;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return ResourceTemplate Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ResourceTemplate
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $annotations = null;
        if (isset($input->{'annotations'})) {
            $annotations = ResourceTemplateAnnotations::buildFromInput($input->{'annotations'}, validate: $validate);
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
        $uriTemplate = $input->{'uriTemplate'};

        $obj = new self($name, $uriTemplate);
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
        $output['uriTemplate'] = $this->uriTemplate;

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

