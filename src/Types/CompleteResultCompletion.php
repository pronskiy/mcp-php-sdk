<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class CompleteResultCompletion
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
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
    ];

    /**
     * Indicates whether there are additional completion options beyond those provided in the current response, even if the exact total is unknown.
     *
     * @var bool|null
     */
    private ?bool $hasMore = null;

    /**
     * The total number of completion options available. This can exceed the number of values actually sent in the response.
     *
     * @var int|null
     */
    private ?int $total = null;

    /**
     * An array of completion values. Must not exceed 100 items.
     *
     * @var string[]
     */
    private array $values;

    /**
     * @param string[] $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @return bool|null
     */
    public function getHasMore() : ?bool
    {
        return $this->hasMore ?? null;
    }

    /**
     * @return int|null
     */
    public function getTotal() : ?int
    {
        return $this->total ?? null;
    }

    /**
     * @return string[]
     */
    public function getValues() : array
    {
        return $this->values;
    }

    /**
     * @param bool $hasMore
     * @return self
     */
    public function withHasMore(bool $hasMore) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($hasMore, static::$schema['properties']['hasMore']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->hasMore = $hasMore;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutHasMore() : self
    {
        $clone = clone $this;
        unset($clone->hasMore);

        return $clone;
    }

    /**
     * @param int $total
     * @return self
     */
    public function withTotal(int $total) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($total, static::$schema['properties']['total']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->total = $total;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutTotal() : self
    {
        $clone = clone $this;
        unset($clone->total);

        return $clone;
    }

    /**
     * @param string[] $values
     * @return self
     */
    public function withValues(array $values) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($values, static::$schema['properties']['values']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->values = $values;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return CompleteResultCompletion Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : CompleteResultCompletion
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $hasMore = null;
        if (isset($input->{'hasMore'})) {
            $hasMore = (bool)($input->{'hasMore'});
        }
        $total = null;
        if (isset($input->{'total'})) {
            $total = (int)($input->{'total'});
        }
        $values = $input->{'values'};

        $obj = new self($values);
        $obj->hasMore = $hasMore;
        $obj->total = $total;
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
        if (isset($this->hasMore)) {
            $output['hasMore'] = $this->hasMore;
        }
        if (isset($this->total)) {
            $output['total'] = $this->total;
        }
        $output['values'] = $this->values;

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

