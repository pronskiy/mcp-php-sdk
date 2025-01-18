<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class CompleteResult
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'The server\'s response to a completion/complete request',
        'properties' => [
            '_meta' => [
                'additionalProperties' => [
                    
                ],
                'description' => 'This result property is reserved by the protocol to allow clients and servers to attach additional metadata to their responses.',
                'type' => 'object',
            ],
            'completion' => [
                'properties' => [
                    'hasMore' => [
                        'description' => 'Indicates whether there are additional completion options beyond those provided in the current response, even if the exact total is unknown.',
                        'type' => 'boolean',
                    ],
                    'total' => [
                        'description' => 'The total number of completion options available. This can exceed the number of values actually sent in the response.',
                        'type' => 'integer',
                    ],
                    'values' => [
                        'description' => 'An array of completion values. Must not exceed 100 items.',
                        'items' => [
                            'type' => 'string',
                        ],
                        'type' => 'array',
                    ],
                ],
                'required' => [
                    'values',
                ],
                'type' => 'object',
            ],
        ],
        'required' => [
            'completion',
        ],
        'type' => 'object',
    ];

    /**
     * This result property is reserved by the protocol to allow clients and servers to attach additional metadata to their responses.
     *
     * @var mixed[]|null
     */
    private ?array $Meta = null;

    /**
     * @var CompleteResultCompletion
     */
    private CompleteResultCompletion $completion;

    /**
     * @param CompleteResultCompletion $completion
     */
    public function __construct(CompleteResultCompletion $completion)
    {
        $this->completion = $completion;
    }

    /**
     * @return mixed[]|null
     */
    public function getMeta() : ?array
    {
        return $this->Meta ?? null;
    }

    /**
     * @return CompleteResultCompletion
     */
    public function getCompletion() : CompleteResultCompletion
    {
        return $this->completion;
    }

    /**
     * @param mixed[] $Meta
     * @return self
     */
    public function withMeta(array $Meta) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($Meta, static::$schema['properties']['_meta']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->Meta = $Meta;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutMeta() : self
    {
        $clone = clone $this;
        unset($clone->Meta);

        return $clone;
    }

    /**
     * @param CompleteResultCompletion $completion
     * @return self
     */
    public function withCompletion(CompleteResultCompletion $completion) : self
    {
        $clone = clone $this;
        $clone->completion = $completion;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return CompleteResult Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : CompleteResult
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $Meta = null;
        if (isset($input->{'_meta'})) {
            $Meta = (array)$input->{'_meta'};
        }
        $completion = CompleteResultCompletion::buildFromInput($input->{'completion'}, validate: $validate);

        $obj = new self($completion);
        $obj->Meta = $Meta;
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
        if (isset($this->Meta)) {
            $output['_meta'] = $this->Meta;
        }
        $output['completion'] = ($this->completion)->toJson();

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
        $this->completion = clone $this->completion;
    }
}

