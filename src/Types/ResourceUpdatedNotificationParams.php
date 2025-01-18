<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ResourceUpdatedNotificationParams
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'properties' => [
            'uri' => [
                'description' => 'The URI of the resource that has been updated. This might be a sub-resource of the one that the client actually subscribed to.',
                'format' => 'uri',
                'type' => 'string',
            ],
        ],
        'required' => [
            'uri',
        ],
        'type' => 'object',
    ];

    /**
     * The URI of the resource that has been updated. This might be a sub-resource of the one that the client actually subscribed to.
     *
     * @var string
     */
    private string $uri;

    /**
     * @param string $uri
     */
    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getUri() : string
    {
        return $this->uri;
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
     * @return ResourceUpdatedNotificationParams Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ResourceUpdatedNotificationParams
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $uri = $input->{'uri'};

        $obj = new self($uri);

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

