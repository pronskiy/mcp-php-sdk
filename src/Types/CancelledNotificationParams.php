<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class CancelledNotificationParams
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
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
    ];

    /**
     * An optional string describing the reason for the cancellation. This MAY be logged or presented to the user.
     *
     * @var string|null
     */
    private ?string $reason = null;

    /**
     * The ID of the request to cancel.
     *
     * This MUST correspond to the ID of a request previously issued in the same direction.
     *
     * @var mixed
     */
    private mixed $requestId;

    /**
     * @param mixed $requestId
     */
    public function __construct(mixed $requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * @return string|null
     */
    public function getReason() : ?string
    {
        return $this->reason ?? null;
    }

    /**
     * @return mixed
     */
    public function getRequestId() : mixed
    {
        return $this->requestId;
    }

    /**
     * @param string $reason
     * @return self
     */
    public function withReason(string $reason) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($reason, static::$schema['properties']['reason']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->reason = $reason;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutReason() : self
    {
        $clone = clone $this;
        unset($clone->reason);

        return $clone;
    }

    /**
     * @param mixed $requestId
     * @return self
     */
    public function withRequestId(mixed $requestId) : self
    {
        $clone = clone $this;
        $clone->requestId = $requestId;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return CancelledNotificationParams Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : CancelledNotificationParams
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $reason = null;
        if (isset($input->{'reason'})) {
            $reason = $input->{'reason'};
        }
        $requestId = $input->{'requestId'};

        $obj = new self($requestId);
        $obj->reason = $reason;
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
        if (isset($this->reason)) {
            $output['reason'] = $this->reason;
        }
        $output['requestId'] = $this->requestId;

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

