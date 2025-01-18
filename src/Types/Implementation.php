<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class Implementation
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'Describes the name and version of an MCP implementation.',
        'properties' => [
            'name' => [
                'type' => 'string',
            ],
            'version' => [
                'type' => 'string',
            ],
        ],
        'required' => [
            'name',
            'version',
        ],
        'type' => 'object',
    ];

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $version;

    /**
     * @param string $name
     * @param string $version
     */
    public function __construct(string $name, string $version)
    {
        $this->name = $name;
        $this->version = $version;
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
    public function getVersion() : string
    {
        return $this->version;
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
     * @param string $version
     * @return self
     */
    public function withVersion(string $version) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($version, static::$schema['properties']['version']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->version = $version;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return Implementation Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : Implementation
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $name = $input->{'name'};
        $version = $input->{'version'};

        $obj = new self($name, $version);

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
        $output['name'] = $this->name;
        $output['version'] = $this->version;

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

