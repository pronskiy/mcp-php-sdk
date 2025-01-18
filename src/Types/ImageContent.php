<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ImageContent
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'An image provided to or from an LLM.',
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
            'data' => [
                'description' => 'The base64-encoded image data.',
                'format' => 'byte',
                'type' => 'string',
            ],
            'mimeType' => [
                'description' => 'The MIME type of the image. Different providers may support different image types.',
                'type' => 'string',
            ],
            'type' => [
                'const' => 'image',
                'type' => 'string',
            ],
        ],
        'required' => [
            'data',
            'mimeType',
            'type',
        ],
        'type' => 'object',
    ];

    /**
     * @var ImageContentAnnotations|null
     */
    private ?ImageContentAnnotations $annotations = null;

    /**
     * The base64-encoded image data.
     *
     * @var string
     */
    private string $data;

    /**
     * The MIME type of the image. Different providers may support different image types.
     *
     * @var string
     */
    private string $mimeType;

    /**
     * @var string
     */
    private string $type;

    /**
     * @param string $data
     * @param string $mimeType
     * @param string $type
     */
    public function __construct(string $data, string $mimeType, string $type)
    {
        $this->data = $data;
        $this->mimeType = $mimeType;
        $this->type = $type;
    }

    /**
     * @return ImageContentAnnotations|null
     */
    public function getAnnotations() : ?ImageContentAnnotations
    {
        return $this->annotations ?? null;
    }

    /**
     * @return string
     */
    public function getData() : string
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getMimeType() : string
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param ImageContentAnnotations $annotations
     * @return self
     */
    public function withAnnotations(ImageContentAnnotations $annotations) : self
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
     * @param string $data
     * @return self
     */
    public function withData(string $data) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($data, static::$schema['properties']['data']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->data = $data;

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
     * @return ImageContent Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ImageContent
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $annotations = null;
        if (isset($input->{'annotations'})) {
            $annotations = ImageContentAnnotations::buildFromInput($input->{'annotations'}, validate: $validate);
        }
        $data = $input->{'data'};
        $mimeType = $input->{'mimeType'};
        $type = $input->{'type'};

        $obj = new self($data, $mimeType, $type);
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
        $output['data'] = $this->data;
        $output['mimeType'] = $this->mimeType;
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
    }
}

