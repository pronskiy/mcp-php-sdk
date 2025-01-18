<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class SamplingMessage
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'Describes a message issued to or received from an LLM API.',
        'properties' => [
            'content' => [
                'anyOf' => [
                    [
                        '$ref' => '#/definitions/TextContent',
                    ],
                    [
                        '$ref' => '#/definitions/ImageContent',
                    ],
                ],
            ],
            'role' => [
                '$ref' => '#/definitions/Role',
            ],
        ],
        'required' => [
            'content',
            'role',
        ],
        'type' => 'object',
    ];

    /**
     * @var mixed
     */
    private mixed $content;

    /**
     * @var mixed
     */
    private mixed $role;

    /**
     * @param mixed $content
     * @param mixed $role
     */
    public function __construct(mixed $content, mixed $role)
    {
        $this->content = $content;
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getContent() : mixed
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getRole() : mixed
    {
        return $this->role;
    }

    /**
     * @param mixed $content
     * @return self
     */
    public function withContent(mixed $content) : self
    {
        $clone = clone $this;
        $clone->content = $content;

        return $clone;
    }

    /**
     * @param mixed $role
     * @return self
     */
    public function withRole(mixed $role) : self
    {
        $clone = clone $this;
        $clone->role = $role;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return SamplingMessage Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : SamplingMessage
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $content = match (true) {
            true => $input->{'content'},
        };
        $role = $input->{'role'};

        $obj = new self($content, $role);

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
        $output['content'] = match (true) {
            true => $this->content,
        };
        $output['role'] = $this->role;

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
        $this->content = match (true) {
            true => $this->content,
        };
    }
}

