<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class CancelledNotification
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'This notification can be sent by either side to indicate that it is cancelling a previously-issued request.

The request SHOULD still be in-flight, but due to communication latency, it is always possible that this notification MAY arrive after the request has already finished.

This notification indicates that the result will be unused, so any associated processing SHOULD cease.

A client MUST NOT attempt to cancel its `initialize` request.',
        'properties' => [
            'method' => [
                'const' => 'notifications/cancelled',
                'type' => 'string',
            ],
            'params' => [
                'properties' => [
                    'reason' => [
                        'description' => 'An optional string describing the reason for the cancellation. This MAY be logged or presented to the user.',
                        'type' => 'string',
                    ],
                    'requestId' => [
                        '$ref' => '#/definitions/RequestId',
                        'description' => 'The ID of the request to cancel.

This MUST correspond to the ID of a request previously issued in the same direction.',
                    ],
                ],
                'required' => [
                    'requestId',
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
     * @var CancelledNotificationParams
     */
    private CancelledNotificationParams $params;

    /**
     * @param string $method
     * @param CancelledNotificationParams $params
     */
    public function __construct(string $method, CancelledNotificationParams $params)
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
     * @return CancelledNotificationParams
     */
    public function getParams() : CancelledNotificationParams
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
     * @param CancelledNotificationParams $params
     * @return self
     */
    public function withParams(CancelledNotificationParams $params) : self
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
     * @return CancelledNotification Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : CancelledNotification
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $method = $input->{'method'};
        $params = CancelledNotificationParams::buildFromInput($input->{'params'}, validate: $validate);

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

