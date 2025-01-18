<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class BlobResourceContents
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'properties' => [
            'blob' => [
                'description' => 'A base64-encoded string representing the binary data of the item.',
                'format' => 'byte',
                'type' => 'string',
            ],
            'mimeType' => [
                'description' => 'The MIME type of this resource, if known.',
                'type' => 'string',
            ],
            'uri' => [
                'description' => 'The URI of this resource.',
                'format' => 'uri',
                'type' => 'string',
            ],
        ],
        'required' => [
            'blob',
            'uri',
        ],
        'type' => 'object',
    ];

    /**
     * A base64-encoded string representing the binary data of the item.
     *
     * @var string
     */
    private string $blob;

    /**
     * The MIME type of this resource, if known.
     *
     * @var string|null
     */
    private ?string $mimeType = null;

    /**
     * The URI of this resource.
     *
     * @var string
     */
    private string $uri;

    /**
     * @param string $blob
     * @param string $uri
     */
    public function __construct(string $blob, string $uri)
    {
        $this->blob = $blob;
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getBlob() : string
    {
        return $this->blob;
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
    public function getUri() : string
    {
        return $this->uri;
    }

    /**
     * @param string $blob
     * @return self
     */
    public function withBlob(string $blob) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($blob, static::$schema['properties']['blob']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->blob = $blob;

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
     * @return BlobResourceContents Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : BlobResourceContents
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $blob = $input->{'blob'};
        $mimeType = null;
        if (isset($input->{'mimeType'})) {
            $mimeType = $input->{'mimeType'};
        }
        $uri = $input->{'uri'};

        $obj = new self($blob, $uri);
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
        $output['blob'] = $this->blob;
        if (isset($this->mimeType)) {
            $output['mimeType'] = $this->mimeType;
        }
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

