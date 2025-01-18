<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ResourceTemplateAnnotations
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'properties' => [
            'audience' => [
                'description' => 'Describes who the intended customer of this object or data is.

It can include multiple entries to indicate content useful for multiple audiences (e.g., `["user", "assistant"]`).',
                'items' => [
                    '$ref' => '#/definitions/Role',
                ],
                'type' => 'array',
            ],
            'priority' => [
                'description' => 'Describes how important this data is for operating the server.

A value of 1 means "most important," and indicates that the data is
effectively required, while 0 means "least important," and indicates that
the data is entirely optional.',
                'maximum' => 1,
                'minimum' => 0,
                'type' => 'number',
            ],
        ],
        'type' => 'object',
    ];

    /**
     * Describes who the intended customer of this object or data is.
     *
     * It can include multiple entries to indicate content useful for multiple audiences (e.g., `["user", "assistant"]`).
     *
     * @var mixed[]|null
     */
    private ?array $audience = null;

    /**
     * Describes how important this data is for operating the server.
     *
     * A value of 1 means "most important," and indicates that the data is
     * effectively required, while 0 means "least important," and indicates that
     * the data is entirely optional.
     *
     * @var int|float|null
     */
    private int|float|null $priority = null;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return mixed[]|null
     */
    public function getAudience() : ?array
    {
        return $this->audience ?? null;
    }

    /**
     * @return int|float|null
     */
    public function getPriority() : int|float|null
    {
        return $this->priority;
    }

    /**
     * @param mixed[] $audience
     * @return self
     */
    public function withAudience(array $audience) : self
    {
        $clone = clone $this;
        $clone->audience = $audience;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutAudience() : self
    {
        $clone = clone $this;
        unset($clone->audience);

        return $clone;
    }

    /**
     * @param int|float $priority
     * @return self
     */
    public function withPriority(int|float $priority) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($priority, static::$schema['properties']['priority']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->priority = $priority;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutPriority() : self
    {
        $clone = clone $this;
        unset($clone->priority);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return ResourceTemplateAnnotations Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ResourceTemplateAnnotations
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $audience = null;
        if (isset($input->{'audience'})) {
            $audience = array_map(fn(mixed $i): mixed => $i, $input->{'audience'});
        }
        $priority = null;
        if (isset($input->{'priority'})) {
            $priority = str_contains((string)($input->{'priority'}), '.') ? (float)($input->{'priority'}) : (int)($input->{'priority'});
        }

        $obj = new self();
        $obj->audience = $audience;
        $obj->priority = $priority;
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
        if (isset($this->audience)) {
            $output['audience'] = array_map(fn(mixed $i): mixed => $i, $this->audience);
        }
        if (isset($this->priority)) {
            $output['priority'] = $this->priority;
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

