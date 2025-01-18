<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class CallToolRequestParams
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'properties' => [
            'arguments' => [
                'additionalProperties' => [
                    
                ],
                'type' => 'object',
            ],
            'name' => [
                'type' => 'string',
            ],
        ],
        'required' => [
            'name',
        ],
        'type' => 'object',
    ];

    /**
     * @var mixed[]|null
     */
    private ?array $arguments = null;

    /**
     * @var string
     */
    private string $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed[]|null
     */
    public function getArguments() : ?array
    {
        return $this->arguments ?? null;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param mixed[] $arguments
     * @return self
     */
    public function withArguments(array $arguments) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($arguments, static::$schema['properties']['arguments']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->arguments = $arguments;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutArguments() : self
    {
        $clone = clone $this;
        unset($clone->arguments);

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
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return CallToolRequestParams Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : CallToolRequestParams
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $arguments = null;
        if (isset($input->{'arguments'})) {
            $arguments = (array)$input->{'arguments'};
        }
        $name = $input->{'name'};

        $obj = new self($name);
        $obj->arguments = $arguments;
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
        if (isset($this->arguments)) {
            $output['arguments'] = $this->arguments;
        }
        $output['name'] = $this->name;

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

