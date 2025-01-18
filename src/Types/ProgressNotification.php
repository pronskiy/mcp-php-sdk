<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ProgressNotification
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'An out-of-band notification used to inform the receiver of a progress update for a long-running request.',
        'properties' => [
            'method' => [
                'const' => 'notifications/progress',
                'type' => 'string',
            ],
            'params' => [
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
     * @var ProgressNotificationParams
     */
    private ProgressNotificationParams $params;

    /**
     * @param string $method
     * @param ProgressNotificationParams $params
     */
    public function __construct(string $method, ProgressNotificationParams $params)
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
     * @return ProgressNotificationParams
     */
    public function getParams() : ProgressNotificationParams
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
     * @param ProgressNotificationParams $params
     * @return self
     */
    public function withParams(ProgressNotificationParams $params) : self
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
     * @return ProgressNotification Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ProgressNotification
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $method = $input->{'method'};
        $params = ProgressNotificationParams::buildFromInput($input->{'params'}, validate: $validate);

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

