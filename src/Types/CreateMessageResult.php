<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class CreateMessageResult
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'The client\'s response to a sampling/create_message request from the server. The client should inform the user before returning the sampled message, to allow them to inspect the response (human in the loop) and decide whether to allow the server to see it.',
        'properties' => [
            '_meta' => [
                'additionalProperties' => [
                    
                ],
                'description' => 'This result property is reserved by the protocol to allow clients and servers to attach additional metadata to their responses.',
                'type' => 'object',
            ],
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
            'model' => [
                'description' => 'The name of the model that generated the message.',
                'type' => 'string',
            ],
            'role' => [
                '$ref' => '#/definitions/Role',
            ],
            'stopReason' => [
                'description' => 'The reason why sampling stopped, if known.',
                'type' => 'string',
            ],
        ],
        'required' => [
            'content',
            'model',
            'role',
        ],
        'type' => 'object',
    ];

    /**
     * This result property is reserved by the protocol to allow clients and servers to attach additional metadata to their responses.
     *
     * @var mixed[]|null
     */
    private ?array $Meta = null;

    /**
     * @var mixed
     */
    private mixed $content;

    /**
     * The name of the model that generated the message.
     *
     * @var string
     */
    private string $model;

    /**
     * @var mixed
     */
    private mixed $role;

    /**
     * The reason why sampling stopped, if known.
     *
     * @var string|null
     */
    private ?string $stopReason = null;

    /**
     * @param mixed $content
     * @param string $model
     * @param mixed $role
     */
    public function __construct(mixed $content, string $model, mixed $role)
    {
        $this->content = $content;
        $this->model = $model;
        $this->role = $role;
    }

    /**
     * @return mixed[]|null
     */
    public function getMeta() : ?array
    {
        return $this->Meta ?? null;
    }

    /**
     * @return mixed
     */
    public function getContent() : mixed
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getModel() : string
    {
        return $this->model;
    }

    /**
     * @return mixed
     */
    public function getRole() : mixed
    {
        return $this->role;
    }

    /**
     * @return string|null
     */
    public function getStopReason() : ?string
    {
        return $this->stopReason ?? null;
    }

    /**
     * @param mixed[] $Meta
     * @return self
     */
    public function withMeta(array $Meta) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($Meta, static::$schema['properties']['_meta']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->Meta = $Meta;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutMeta() : self
    {
        $clone = clone $this;
        unset($clone->Meta);

        return $clone;
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
     * @param string $model
     * @return self
     */
    public function withModel(string $model) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($model, static::$schema['properties']['model']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->model = $model;

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
     * @param string $stopReason
     * @return self
     */
    public function withStopReason(string $stopReason) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($stopReason, static::$schema['properties']['stopReason']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->stopReason = $stopReason;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutStopReason() : self
    {
        $clone = clone $this;
        unset($clone->stopReason);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return CreateMessageResult Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : CreateMessageResult
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $Meta = null;
        if (isset($input->{'_meta'})) {
            $Meta = (array)$input->{'_meta'};
        }
        $content = match (true) {
            true => $input->{'content'},
        };
        $model = $input->{'model'};
        $role = $input->{'role'};
        $stopReason = null;
        if (isset($input->{'stopReason'})) {
            $stopReason = $input->{'stopReason'};
        }

        $obj = new self($content, $model, $role);
        $obj->Meta = $Meta;
        $obj->stopReason = $stopReason;
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
        if (isset($this->Meta)) {
            $output['_meta'] = $this->Meta;
        }
        $output['content'] = match (true) {
            true => $this->content,
        };
        $output['model'] = $this->model;
        $output['role'] = $this->role;
        if (isset($this->stopReason)) {
            $output['stopReason'] = $this->stopReason;
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
        $this->content = match (true) {
            true => $this->content,
        };
    }
}

