<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class PaginatedRequest
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'properties' => [
            'method' => [
                'type' => 'string',
            ],
            'params' => [
                'properties' => [
                    'cursor' => [
                        'description' => 'An opaque token representing the current pagination position.
If provided, the server should return results starting after this cursor.',
                        'type' => 'string',
                    ],
                ],
                'type' => 'object',
            ],
        ],
        'required' => [
            'method',
        ],
        'type' => 'object',
    ];

    /**
     * @var string
     */
    private string $method;

    /**
     * @var PaginatedRequestParams|null
     */
    private ?PaginatedRequestParams $params = null;

    /**
     * @param string $method
     */
    public function __construct(string $method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * @return PaginatedRequestParams|null
     */
    public function getParams() : ?PaginatedRequestParams
    {
        return $this->params ?? null;
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
     * @param PaginatedRequestParams $params
     * @return self
     */
    public function withParams(PaginatedRequestParams $params) : self
    {
        $clone = clone $this;
        $clone->params = $params;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutParams() : self
    {
        $clone = clone $this;
        unset($clone->params);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return PaginatedRequest Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : PaginatedRequest
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $method = $input->{'method'};
        $params = null;
        if (isset($input->{'params'})) {
            $params = PaginatedRequestParams::buildFromInput($input->{'params'}, validate: $validate);
        }

        $obj = new self($method);
        $obj->params = $params;
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
        if (isset($this->params)) {
            $output['params'] = ($this->params)->toJson();
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
        if (isset($this->params)) {
            $this->params = clone $this->params;
        }
    }
}

