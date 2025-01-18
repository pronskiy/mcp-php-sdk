<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class Tool
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'Definition for a tool the client can call.',
        'properties' => [
            'description' => [
                'description' => 'A human-readable description of the tool.',
                'type' => 'string',
            ],
            'inputSchema' => [
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
            ],
            'name' => [
                'description' => 'The name of the tool.',
                'type' => 'string',
            ],
        ],
        'required' => [
            'inputSchema',
            'name',
        ],
        'type' => 'object',
    ];

    /**
     * A human-readable description of the tool.
     *
     * @var string|null
     */
    private ?string $description = null;

    /**
     * A JSON Schema object defining the expected parameters for the tool.
     *
     * @var ToolInputSchema
     */
    private ToolInputSchema $inputSchema;

    /**
     * The name of the tool.
     *
     * @var string
     */
    private string $name;

    /**
     * @param ToolInputSchema $inputSchema
     * @param string $name
     */
    public function __construct(ToolInputSchema $inputSchema, string $name)
    {
        $this->inputSchema = $inputSchema;
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
     * @return ToolInputSchema
     */
    public function getInputSchema() : ToolInputSchema
    {
        return $this->inputSchema;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
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
     * @param ToolInputSchema $inputSchema
     * @return self
     */
    public function withInputSchema(ToolInputSchema $inputSchema) : self
    {
        $clone = clone $this;
        $clone->inputSchema = $inputSchema;

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
     * @return Tool Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : Tool
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $description = null;
        if (isset($input->{'description'})) {
            $description = $input->{'description'};
        }
        $inputSchema = ToolInputSchema::buildFromInput($input->{'inputSchema'}, validate: $validate);
        $name = $input->{'name'};

        $obj = new self($inputSchema, $name);
        $obj->description = $description;
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
        $output['inputSchema'] = ($this->inputSchema)->toJson();
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
        $this->inputSchema = clone $this->inputSchema;
    }
}

