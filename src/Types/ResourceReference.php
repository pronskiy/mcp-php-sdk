<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ResourceReference
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'A reference to a resource or resource template definition.',
        'properties' => [
            'type' => [
                'const' => 'ref/resource',
                'type' => 'string',
            ],
            'uri' => [
                'description' => 'The URI or URI template of the resource.',
                'format' => 'uri-template',
                'type' => 'string',
            ],
        ],
        'required' => [
            'type',
            'uri',
        ],
        'type' => 'object',
    ];

    /**
     * @var string
     */
    private string $type;

    /**
     * The URI or URI template of the resource.
     *
     * @var string
     */
    private string $uri;

    /**
     * @param string $type
     * @param string $uri
     */
    public function __construct(string $type, string $uri)
    {
        $this->type = $type;
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUri() : string
    {
        return $this->uri;
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
     * @return ResourceReference Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ResourceReference
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $type = $input->{'type'};
        $uri = $input->{'uri'};

        $obj = new self($type, $uri);

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
        $output['type'] = $this->type;
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

