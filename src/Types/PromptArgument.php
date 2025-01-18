<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class PromptArgument
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'Describes an argument that a prompt can accept.',
        'properties' => [
            'description' => [
                'description' => 'A human-readable description of the argument.',
                'type' => 'string',
            ],
            'name' => [
                'description' => 'The name of the argument.',
                'type' => 'string',
            ],
            'required' => [
                'description' => 'Whether this argument must be provided.',
                'type' => 'boolean',
            ],
        ],
        'required' => [
            'name',
        ],
        'type' => 'object',
    ];

    /**
     * A human-readable description of the argument.
     *
     * @var string|null
     */
    private ?string $description = null;

    /**
     * The name of the argument.
     *
     * @var string
     */
    private string $name;

    /**
     * Whether this argument must be provided.
     *
     * @var bool|null
     */
    private ?bool $required = null;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription() : ?string
    {
        return $this->description ?? null;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return bool|null
     */
    public function getRequired() : ?bool
    {
        return $this->required ?? null;
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
     * @param bool $required
     * @return self
     */
    public function withRequired(bool $required) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($required, static::$schema['properties']['required']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->required = $required;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutRequired() : self
    {
        $clone = clone $this;
        unset($clone->required);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return PromptArgument Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : PromptArgument
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $description = null;
        if (isset($input->{'description'})) {
            $description = $input->{'description'};
        }
        $name = $input->{'name'};
        $required = null;
        if (isset($input->{'required'})) {
            $required = (bool)($input->{'required'});
        }

        $obj = new self($name);
        $obj->description = $description;
        $obj->required = $required;
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
        if (isset($this->description)) {
            $output['description'] = $this->description;
        }
        $output['name'] = $this->name;
        if (isset($this->required)) {
            $output['required'] = $this->required;
        }

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

