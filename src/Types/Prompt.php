<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class Prompt
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'A prompt or prompt template that the server offers.',
        'properties' => [
            'arguments' => [
                'description' => 'A list of arguments to use for templating the prompt.',
                'items' => [
                    '$ref' => '#/definitions/PromptArgument',
                ],
                'type' => 'array',
            ],
            'description' => [
                'description' => 'An optional description of what this prompt provides',
                'type' => 'string',
            ],
            'name' => [
                'description' => 'The name of the prompt or prompt template.',
                'type' => 'string',
            ],
        ],
        'required' => [
            'name',
        ],
        'type' => 'object',
    ];

    /**
     * A list of arguments to use for templating the prompt.
     *
     * @var mixed[]|null
     */
    private ?array $arguments = null;

    /**
     * An optional description of what this prompt provides
     *
     * @var string|null
     */
    private ?string $description = null;

    /**
     * The name of the prompt or prompt template.
     *
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
     * @param mixed[] $arguments
     * @return self
     */
    public function withArguments(array $arguments) : self
    {
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
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return Prompt Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : Prompt
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $arguments = null;
        if (isset($input->{'arguments'})) {
            $arguments = array_map(fn(mixed $i): mixed => $i, $input->{'arguments'});
        }
        $description = null;
        if (isset($input->{'description'})) {
            $description = $input->{'description'};
        }
        $name = $input->{'name'};

        $obj = new self($name);
        $obj->arguments = $arguments;
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
        if (isset($this->arguments)) {
            $output['arguments'] = array_map(fn(mixed $i): mixed => $i, $this->arguments);
        }
        if (isset($this->description)) {
            $output['description'] = $this->description;
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

