<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ToolInputSchema
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'A JSON Schema object defining the expected parameters for the tool.',
        'properties' => [
            'properties' => [
                'additionalProperties' => [
                    'additionalProperties' => true,
                    'properties' => [
                        
                    ],
                    'type' => 'object',
                ],
                'type' => 'object',
            ],
            'required' => [
                'items' => [
                    'type' => 'string',
                ],
                'type' => 'array',
            ],
            'type' => [
                'const' => 'object',
                'type' => 'string',
            ],
        ],
        'required' => [
            'type',
        ],
        'type' => 'object',
    ];

    /**
     * @var ToolInputSchemaPropertiesItem[]|null
     */
    private ?array $properties = null;

    /**
     * @var string[]|null
     */
    private ?array $required = null;

    /**
     * @var string
     */
    private string $type;

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return ToolInputSchemaPropertiesItem[]|null
     */
    public function getProperties() : ?array
    {
        return $this->properties ?? null;
    }

    /**
     * @return string[]|null
     */
    public function getRequired() : ?array
    {
        return $this->required ?? null;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param ToolInputSchemaPropertiesItem[] $properties
     * @return self
     */
    public function withProperties(array $properties) : self
    {
        $clone = clone $this;
        $clone->properties = $properties;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutProperties() : self
    {
        $clone = clone $this;
        unset($clone->properties);

        return $clone;
    }

    /**
     * @param string[] $required
     * @return self
     */
    public function withRequired(array $required) : self
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
     * @return ToolInputSchema Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ToolInputSchema
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $properties = null;
        if (isset($input->{'properties'})) {
            $properties = array_map(fn (array|object $i): ToolInputSchemaPropertiesItem => ToolInputSchemaPropertiesItem::buildFromInput($i, validate: $validate), $input->{'properties'});
        }
        $required = null;
        if (isset($input->{'required'})) {
            $required = $input->{'required'};
        }
        $type = $input->{'type'};

        $obj = new self($type);
        $obj->properties = $properties;
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
        if (isset($this->properties)) {
            $output['properties'] = array_map(fn (ToolInputSchemaPropertiesItem $i) => $i->toJson(), $this->properties);
        }
        if (isset($this->required)) {
            $output['required'] = $this->required;
        }
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
        if (isset($this->properties)) {
            $this->properties = array_map(fn (ToolInputSchemaPropertiesItem $i) => clone $i, $this->properties);
        }
    }
}

