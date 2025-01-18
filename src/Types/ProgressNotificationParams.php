<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ProgressNotificationParams
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'properties' => [
            'progress' => [
                'description' => 'The progress thus far. This should increase every time progress is made, even if the total is unknown.',
                'type' => 'number',
            ],
            'progressToken' => [
                '$ref' => '#/definitions/ProgressToken',
                'description' => 'The progress token which was given in the initial request, used to associate this notification with the request that is proceeding.',
            ],
            'total' => [
                'description' => 'Total number of items to process (or total progress required), if known.',
                'type' => 'number',
            ],
        ],
        'required' => [
            'progress',
            'progressToken',
        ],
        'type' => 'object',
    ];

    /**
     * The progress thus far. This should increase every time progress is made, even if the total is unknown.
     *
     * @var int|float
     */
    private int|float $progress;

    /**
     * The progress token which was given in the initial request, used to associate this notification with the request that is proceeding.
     *
     * @var mixed
     */
    private mixed $progressToken;

    /**
     * Total number of items to process (or total progress required), if known.
     *
     * @var int|float|null
     */
    private int|float|null $total = null;

    /**
     * @param int|float $progress
     * @param mixed $progressToken
     */
    public function __construct(int|float $progress, mixed $progressToken)
    {
        $this->progress = $progress;
        $this->progressToken = $progressToken;
    }

    /**
     * @return int|float
     */
    public function getProgress() : int|float
    {
        return $this->progress;
    }

    /**
     * @return mixed
     */
    public function getProgressToken() : mixed
    {
        return $this->progressToken;
    }

    /**
     * @return int|float|null
     */
    public function getTotal() : int|float|null
    {
        return $this->total;
    }

    /**
     * @param int|float $progress
     * @return self
     */
    public function withProgress(int|float $progress) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($progress, static::$schema['properties']['progress']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->progress = $progress;

        return $clone;
    }

    /**
     * @param mixed $progressToken
     * @return self
     */
    public function withProgressToken(mixed $progressToken) : self
    {
        $clone = clone $this;
        $clone->progressToken = $progressToken;

        return $clone;
    }

    /**
     * @param int|float $total
     * @return self
     */
    public function withTotal(int|float $total) : self
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
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return ProgressNotificationParams Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ProgressNotificationParams
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $progress = str_contains((string)($input->{'progress'}), '.') ? (float)($input->{'progress'}) : (int)($input->{'progress'});
        $progressToken = $input->{'progressToken'};
        $total = null;
        if (isset($input->{'total'})) {
            $total = str_contains((string)($input->{'total'}), '.') ? (float)($input->{'total'}) : (int)($input->{'total'});
        }

        $obj = new self($progress, $progressToken);
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
        $output['progress'] = $this->progress;
        $output['progressToken'] = $this->progressToken;
        if (isset($this->total)) {
            $output['total'] = $this->total;
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

