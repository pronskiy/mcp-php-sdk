<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class LoggingMessageNotification
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'Notification of a log message passed from server to client. If no logging/setLevel request has been sent from the client, the server MAY decide which messages to send automatically.',
        'properties' => [
            'method' => [
                'const' => 'notifications/message',
                'type' => 'string',
            ],
            'params' => [
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
            ],
        ],
        'required' => [
            'method',
            'params',
        ],
        'type' => 'object',
    ];

    /**
     * @var string
     */
    private string $method;

    /**
     * @var LoggingMessageNotificationParams
     */
    private LoggingMessageNotificationParams $params;

    /**
     * @param string $method
     * @param LoggingMessageNotificationParams $params
     */
    public function __construct(string $method, LoggingMessageNotificationParams $params)
    {
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * @return LoggingMessageNotificationParams
     */
    public function getParams() : LoggingMessageNotificationParams
    {
        return $this->params;
    }

    /**
     * @param string $method
     * @return self
     */
    public function withMethod(string $method) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($method, static::$schema['properties']['method']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    /**
     * @param LoggingMessageNotificationParams $params
     * @return self
     */
    public function withParams(LoggingMessageNotificationParams $params) : self
    {
        $clone = clone $this;
        $clone->params = $params;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return LoggingMessageNotification Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : LoggingMessageNotification
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $method = $input->{'method'};
        $params = LoggingMessageNotificationParams::buildFromInput($input->{'params'}, validate: $validate);

        $obj = new self($method, $params);

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
        $output['method'] = $this->method;
        $output['params'] = ($this->params)->toJson();

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
        $this->params = clone $this->params;
    }
}

