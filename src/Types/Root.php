<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class Root
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'Represents a root directory or file that the server can operate on.',
        'properties' => [
            'name' => [
                'description' => 'An optional name for the root. This can be used to provide a human-readable
identifier for the root, which may be useful for display purposes or for
referencing the root in other parts of the application.',
                'type' => 'string',
            ],
            'uri' => [
                'description' => 'The URI identifying the root. This *must* start with file:// for now.
This restriction may be relaxed in future versions of the protocol to allow
other URI schemes.',
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
     * An optional name for the root. This can be used to provide a human-readable
     * identifier for the root, which may be useful for display purposes or for
     * referencing the root in other parts of the application.
     *
     * @var string|null
     */
    private ?string $name = null;

    /**
     * The URI identifying the root. This *must* start with file:// for now.
     * This restriction may be relaxed in future versions of the protocol to allow
     * other URI schemes.
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
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->name ?? null;
    }

    /**
     * @return string
     */
    public function getUri() : string
    {
        return $this->uri;
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
     * @return self
     */
    public function withoutName() : self
    {
        $clone = clone $this;
        unset($clone->name);

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
     * @return Root Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : Root
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $name = null;
        if (isset($input->{'name'})) {
            $name = $input->{'name'};
        }
        $uri = $input->{'uri'};

        $obj = new self($uri);
        $obj->name = $name;
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
        if (isset($this->name)) {
            $output['name'] = $this->name;
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

