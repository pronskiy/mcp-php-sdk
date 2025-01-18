<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class LoggingMessageNotificationParams
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'properties' => [
            'data' => [
                'description' => 'The data to be logged, such as a string message or an object. Any JSON serializable type is allowed here.',
            ],
            'level' => [
                '$ref' => '#/definitions/LoggingLevel',
                'description' => 'The severity of this log message.',
            ],
            'logger' => [
                'description' => 'An optional name of the logger issuing this message.',
                'type' => 'string',
            ],
        ],
        'required' => [
            'data',
            'level',
        ],
        'type' => 'object',
    ];

    /**
     * The data to be logged, such as a string message or an object. Any JSON serializable type is allowed here.
     *
     * @var mixed
     */
    private $data;

    /**
     * The severity of this log message.
     *
     * @var mixed
     */
    private mixed $level;

    /**
     * An optional name of the logger issuing this message.
     *
     * @var string|null
     */
    private ?string $logger = null;

    /**
     * @param mixed $data
     * @param mixed $level
     */
    public function __construct($data, mixed $level)
    {
        $this->data = $data;
        $this->level = $level;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getLevel() : mixed
    {
        return $this->level;
    }

    /**
     * @return string|null
     */
    public function getLogger() : ?string
    {
        return $this->logger ?? null;
    }

    /**
     * @param mixed $data
     * @return self
     */
    public function withData($data) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($data, static::$schema['properties']['data']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->data = $data;

        return $clone;
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
     * @param string $logger
     * @return self
     */
    public function withLogger(string $logger) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($logger, static::$schema['properties']['logger']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->logger = $logger;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutLogger() : self
    {
        $clone = clone $this;
        unset($clone->logger);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return LoggingMessageNotificationParams Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : LoggingMessageNotificationParams
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $data = $input->{'data'};
        $level = $input->{'level'};
        $logger = null;
        if (isset($input->{'logger'})) {
            $logger = $input->{'logger'};
        }

        $obj = new self($data, $level);
        $obj->logger = $logger;
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
        $output['data'] = $this->data;
        $output['level'] = $this->level;
        if (isset($this->logger)) {
            $output['logger'] = $this->logger;
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

