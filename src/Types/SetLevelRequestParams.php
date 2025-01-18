<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class SetLevelRequestParams
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'properties' => [
            'level' => [
                '$ref' => '#/definitions/LoggingLevel',
                'description' => 'The level of logging that the client wants to receive from the server. The server should send all logs at this level and higher (i.e., more severe) to the client as notifications/logging/message.',
            ],
        ],
        'required' => [
            'level',
        ],
        'type' => 'object',
    ];

    /**
     * The level of logging that the client wants to receive from the server. The server should send all logs at this level and higher (i.e., more severe) to the client as notifications/logging/message.
     *
     * @var mixed
     */
    private mixed $level;

    /**
     * @param mixed $level
     */
    public function __construct(mixed $level)
    {
        $this->level = $level;
    }

    /**
     * @return mixed
     */
    public function getLevel() : mixed
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     * @return self
     */
    public function withLevel(mixed $level) : self
    {
        $clone = clone $this;
        $clone->level = $level;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return SetLevelRequestParams Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : SetLevelRequestParams
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $level = $input->{'level'};

        $obj = new self($level);

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
        $output['level'] = $this->level;

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

