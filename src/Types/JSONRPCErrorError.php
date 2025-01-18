<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class JSONRPCErrorError
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'properties' => [
            'code' => [
                'description' => 'The error type that occurred.',
                'type' => 'integer',
            ],
            'data' => [
                'description' => 'Additional information about the error. The value of this member is defined by the sender (e.g. detailed error information, nested errors etc.).',
            ],
            'message' => [
                'description' => 'A short description of the error. The message SHOULD be limited to a concise single sentence.',
                'type' => 'string',
            ],
        ],
        'required' => [
            'code',
            'message',
        ],
        'type' => 'object',
    ];

    /**
     * The error type that occurred.
     *
     * @var int
     */
    private int $code;

    /**
     * Additional information about the error. The value of this member is defined by the sender (e.g. detailed error information, nested errors etc.).
     *
     * @var mixed|null
     */
    private $data = null;

    /**
     * A short description of the error. The message SHOULD be limited to a concise single sentence.
     *
     * @var string
     */
    private string $message;

    /**
     * @param int $code
     * @param string $message
     */
    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getCode() : int
    {
        return $this->code;
    }

    /**
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @param int $code
     * @return self
     */
    public function withCode(int $code) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($code, static::$schema['properties']['code']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->code = $code;

        return $clone;
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
     * @return self
     */
    public function withoutData() : self
    {
        $clone = clone $this;
        unset($clone->data);

        return $clone;
    }

    /**
     * @param string $message
     * @return self
     */
    public function withMessage(string $message) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($message, static::$schema['properties']['message']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->message = $message;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return JSONRPCErrorError Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : JSONRPCErrorError
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $code = (int)($input->{'code'});
        $data = null;
        if (isset($input->{'data'})) {
            $data = $input->{'data'};
        }
        $message = $input->{'message'};

        $obj = new self($code, $message);
        $obj->data = $data;
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
        $output['code'] = $this->code;
        if (isset($this->data)) {
            $output['data'] = $this->data;
        }
        $output['message'] = $this->message;

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

