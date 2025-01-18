<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ModelHint
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'Hints to use for model selection.

Keys not declared here are currently left unspecified by the spec and are up
to the client to interpret.',
        'properties' => [
            'name' => [
                'description' => 'A hint for a model name.

The client SHOULD treat this as a substring of a model name; for example:
 - `claude-3-5-sonnet` should match `claude-3-5-sonnet-20241022`
 - `sonnet` should match `claude-3-5-sonnet-20241022`, `claude-3-sonnet-20240229`, etc.
 - `claude` should match any Claude model

The client MAY also map the string to a different provider\'s model name or a different model family, as long as it fills a similar niche; for example:
 - `gemini-1.5-flash` could match `claude-3-haiku-20240307`',
                'type' => 'string',
            ],
        ],
        'type' => 'object',
    ];

    /**
     * A hint for a model name.
     *
     * The client SHOULD treat this as a substring of a model name; for example:
     *  - `claude-3-5-sonnet` should match `claude-3-5-sonnet-20241022`
     *  - `sonnet` should match `claude-3-5-sonnet-20241022`, `claude-3-sonnet-20240229`, etc.
     *  - `claude` should match any Claude model
     *
     * The client MAY also map the string to a different provider's model name or a different model family, as long as it fills a similar niche; for example:
     *  - `gemini-1.5-flash` could match `claude-3-haiku-20240307`
     *
     * @var string|null
     */
    private ?string $name = null;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->name ?? null;
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
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return ModelHint Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ModelHint
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $name = null;
        if (isset($input->{'name'})) {
            $name = $input->{'name'};
        }

        $obj = new self();
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

