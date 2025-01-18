<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class TextResourceContents
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'properties' => [
            'mimeType' => [
                'description' => 'The MIME type of this resource, if known.',
                'type' => 'string',
            ],
            'text' => [
                'description' => 'The text of the item. This must only be set if the item can actually be represented as text (not binary data).',
                'type' => 'string',
            ],
            'uri' => [
                'description' => 'The URI of this resource.',
                'format' => 'uri',
                'type' => 'string',
            ],
        ],
        'required' => [
            'text',
            'uri',
        ],
        'type' => 'object',
    ];

    /**
     * The MIME type of this resource, if known.
     *
     * @var string|null
     */
    private ?string $mimeType = null;

    /**
     * The text of the item. This must only be set if the item can actually be represented as text (not binary data).
     *
     * @var string
     */
    private string $text;

    /**
     * The URI of this resource.
     *
     * @var string
     */
    private string $uri;

    /**
     * @param string $text
     * @param string $uri
     */
    public function __construct(string $text, string $uri)
    {
        $this->text = $text;
        $this->uri = $uri;
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
    public function getText() : string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getUri() : string
    {
        return $this->uri;
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
     * @param string $text
     * @return self
     */
    public function withText(string $text) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($text, static::$schema['properties']['text']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->text = $text;

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
     * @return TextResourceContents Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : TextResourceContents
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $mimeType = null;
        if (isset($input->{'mimeType'})) {
            $mimeType = $input->{'mimeType'};
        }
        $text = $input->{'text'};
        $uri = $input->{'uri'};

        $obj = new self($text, $uri);
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
        if (isset($this->mimeType)) {
            $output['mimeType'] = $this->mimeType;
        }
        $output['text'] = $this->text;
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
    }
}

