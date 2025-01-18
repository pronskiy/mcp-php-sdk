<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class CompleteRequestParams
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'properties' => [
            'argument' => [
                'description' => 'The argument\'s information',
                'properties' => [
                    'name' => [
                        'description' => 'The name of the argument',
                        'type' => 'string',
                    ],
                    'value' => [
                        'description' => 'The value of the argument to use for completion matching.',
                        'type' => 'string',
                    ],
                ],
                'required' => [
                    'name',
                    'value',
                ],
                'type' => 'object',
            ],
            'ref' => [
                'anyOf' => [
                    [
                        '$ref' => '#/definitions/PromptReference',
                    ],
                    [
                        '$ref' => '#/definitions/ResourceReference',
                    ],
                ],
            ],
        ],
        'required' => [
            'argument',
            'ref',
        ],
        'type' => 'object',
    ];

    /**
     * The argument's information
     *
     * @var CompleteRequestParamsArgument
     */
    private CompleteRequestParamsArgument $argument;

    /**
     * @var mixed
     */
    private mixed $ref;

    /**
     * @param CompleteRequestParamsArgument $argument
     * @param mixed $ref
     */
    public function __construct(CompleteRequestParamsArgument $argument, mixed $ref)
    {
        $this->argument = $argument;
        $this->ref = $ref;
    }

    /**
     * @return CompleteRequestParamsArgument
     */
    public function getArgument() : CompleteRequestParamsArgument
    {
        return $this->argument;
    }

    /**
     * @return mixed
     */
    public function getRef() : mixed
    {
        return $this->ref;
    }

    /**
     * @param CompleteRequestParamsArgument $argument
     * @return self
     */
    public function withArgument(CompleteRequestParamsArgument $argument) : self
    {
        $clone = clone $this;
        $clone->argument = $argument;

        return $clone;
    }

    /**
     * @param mixed $ref
     * @return self
     */
    public function withRef(mixed $ref) : self
    {
        $clone = clone $this;
        $clone->ref = $ref;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return CompleteRequestParams Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : CompleteRequestParams
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $argument = CompleteRequestParamsArgument::buildFromInput($input->{'argument'}, validate: $validate);
        $ref = match (true) {
            true => $input->{'ref'},
        };

        $obj = new self($argument, $ref);

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
        $output['argument'] = ($this->argument)->toJson();
        $output['ref'] = match (true) {
            true => $this->ref,
        };

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
        $this->argument = clone $this->argument;
        $this->ref = match (true) {
            true => $this->ref,
        };
    }
}

