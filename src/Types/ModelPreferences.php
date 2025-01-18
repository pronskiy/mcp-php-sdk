<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ModelPreferences
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'The server\'s preferences for model selection, requested of the client during sampling.

Because LLMs can vary along multiple dimensions, choosing the "best" model is
rarely straightforward.  Different models excel in different areas—some are
faster but less capable, others are more capable but more expensive, and so
on. This interface allows servers to express their priorities across multiple
dimensions to help clients make an appropriate selection for their use case.

These preferences are always advisory. The client MAY ignore them. It is also
up to the client to decide how to interpret these preferences and how to
balance them against other considerations.',
        'properties' => [
            'costPriority' => [
                'description' => 'How much to prioritize cost when selecting a model. A value of 0 means cost
is not important, while a value of 1 means cost is the most important
factor.',
                'maximum' => 1,
                'minimum' => 0,
                'type' => 'number',
            ],
            'hints' => [
                'description' => 'Optional hints to use for model selection.

If multiple hints are specified, the client MUST evaluate them in order
(such that the first match is taken).

The client SHOULD prioritize these hints over the numeric priorities, but
MAY still use the priorities to select from ambiguous matches.',
                'items' => [
                    '$ref' => '#/definitions/ModelHint',
                ],
                'type' => 'array',
            ],
            'intelligencePriority' => [
                'description' => 'How much to prioritize intelligence and capabilities when selecting a
model. A value of 0 means intelligence is not important, while a value of 1
means intelligence is the most important factor.',
                'maximum' => 1,
                'minimum' => 0,
                'type' => 'number',
            ],
            'speedPriority' => [
                'description' => 'How much to prioritize sampling speed (latency) when selecting a model. A
value of 0 means speed is not important, while a value of 1 means speed is
the most important factor.',
                'maximum' => 1,
                'minimum' => 0,
                'type' => 'number',
            ],
        ],
        'type' => 'object',
    ];

    /**
     * How much to prioritize cost when selecting a model. A value of 0 means cost
     * is not important, while a value of 1 means cost is the most important
     * factor.
     *
     * @var int|float|null
     */
    private int|float|null $costPriority = null;

    /**
     * Optional hints to use for model selection.
     *
     * If multiple hints are specified, the client MUST evaluate them in order
     * (such that the first match is taken).
     *
     * The client SHOULD prioritize these hints over the numeric priorities, but
     * MAY still use the priorities to select from ambiguous matches.
     *
     * @var mixed[]|null
     */
    private ?array $hints = null;

    /**
     * How much to prioritize intelligence and capabilities when selecting a
     * model. A value of 0 means intelligence is not important, while a value of 1
     * means intelligence is the most important factor.
     *
     * @var int|float|null
     */
    private int|float|null $intelligencePriority = null;

    /**
     * How much to prioritize sampling speed (latency) when selecting a model. A
     * value of 0 means speed is not important, while a value of 1 means speed is
     * the most important factor.
     *
     * @var int|float|null
     */
    private int|float|null $speedPriority = null;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return int|float|null
     */
    public function getCostPriority() : int|float|null
    {
        return $this->costPriority;
    }

    /**
     * @return mixed[]|null
     */
    public function getHints() : ?array
    {
        return $this->hints ?? null;
    }

    /**
     * @return int|float|null
     */
    public function getIntelligencePriority() : int|float|null
    {
        return $this->intelligencePriority;
    }

    /**
     * @return int|float|null
     */
    public function getSpeedPriority() : int|float|null
    {
        return $this->speedPriority;
    }

    /**
     * @param int|float $costPriority
     * @return self
     */
    public function withCostPriority(int|float $costPriority) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($costPriority, static::$schema['properties']['costPriority']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->costPriority = $costPriority;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutCostPriority() : self
    {
        $clone = clone $this;
        unset($clone->costPriority);

        return $clone;
    }

    /**
     * @param mixed[] $hints
     * @return self
     */
    public function withHints(array $hints) : self
    {
        $clone = clone $this;
        $clone->hints = $hints;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutHints() : self
    {
        $clone = clone $this;
        unset($clone->hints);

        return $clone;
    }

    /**
     * @param int|float $intelligencePriority
     * @return self
     */
    public function withIntelligencePriority(int|float $intelligencePriority) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($intelligencePriority, static::$schema['properties']['intelligencePriority']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->intelligencePriority = $intelligencePriority;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutIntelligencePriority() : self
    {
        $clone = clone $this;
        unset($clone->intelligencePriority);

        return $clone;
    }

    /**
     * @param int|float $speedPriority
     * @return self
     */
    public function withSpeedPriority(int|float $speedPriority) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($speedPriority, static::$schema['properties']['speedPriority']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->speedPriority = $speedPriority;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutSpeedPriority() : self
    {
        $clone = clone $this;
        unset($clone->speedPriority);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return ModelPreferences Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ModelPreferences
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $costPriority = null;
        if (isset($input->{'costPriority'})) {
            $costPriority = str_contains((string)($input->{'costPriority'}), '.') ? (float)($input->{'costPriority'}) : (int)($input->{'costPriority'});
        }
        $hints = null;
        if (isset($input->{'hints'})) {
            $hints = array_map(fn(mixed $i): mixed => $i, $input->{'hints'});
        }
        $intelligencePriority = null;
        if (isset($input->{'intelligencePriority'})) {
            $intelligencePriority = str_contains((string)($input->{'intelligencePriority'}), '.') ? (float)($input->{'intelligencePriority'}) : (int)($input->{'intelligencePriority'});
        }
        $speedPriority = null;
        if (isset($input->{'speedPriority'})) {
            $speedPriority = str_contains((string)($input->{'speedPriority'}), '.') ? (float)($input->{'speedPriority'}) : (int)($input->{'speedPriority'});
        }

        $obj = new self();
        $obj->costPriority = $costPriority;
        $obj->hints = $hints;
        $obj->intelligencePriority = $intelligencePriority;
        $obj->speedPriority = $speedPriority;
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
        if (isset($this->costPriority)) {
            $output['costPriority'] = $this->costPriority;
        }
        if (isset($this->hints)) {
            $output['hints'] = array_map(fn(mixed $i): mixed => $i, $this->hints);
        }
        if (isset($this->intelligencePriority)) {
            $output['intelligencePriority'] = $this->intelligencePriority;
        }
        if (isset($this->speedPriority)) {
            $output['speedPriority'] = $this->speedPriority;
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

