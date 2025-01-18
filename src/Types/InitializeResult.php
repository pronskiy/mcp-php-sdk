<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class InitializeResult
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'After receiving an initialize request from the client, the server sends this response.',
        'properties' => [
            '_meta' => [
                'additionalProperties' => [
                    
                ],
                'description' => 'This result property is reserved by the protocol to allow clients and servers to attach additional metadata to their responses.',
                'type' => 'object',
            ],
            'capabilities' => [
                '$ref' => '#/definitions/ServerCapabilities',
            ],
            'instructions' => [
                'description' => 'Instructions describing how to use the server and its features.

This can be used by clients to improve the LLM\'s understanding of available tools, resources, etc. It can be thought of like a "hint" to the model. For example, this information MAY be added to the system prompt.',
                'type' => 'string',
            ],
            'protocolVersion' => [
                'description' => 'The version of the Model Context Protocol that the server wants to use. This may not match the version that the client requested. If the client cannot support this version, it MUST disconnect.',
                'type' => 'string',
            ],
            'serverInfo' => [
                '$ref' => '#/definitions/Implementation',
            ],
        ],
        'required' => [
            'capabilities',
            'protocolVersion',
            'serverInfo',
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
    private mixed $capabilities;

    /**
     * Instructions describing how to use the server and its features.
     *
     * This can be used by clients to improve the LLM's understanding of available tools, resources, etc. It can be thought of like a "hint" to the model. For example, this information MAY be added to the system prompt.
     *
     * @var string|null
     */
    private ?string $instructions = null;

    /**
     * The version of the Model Context Protocol that the server wants to use. This may not match the version that the client requested. If the client cannot support this version, it MUST disconnect.
     *
     * @var string
     */
    private string $protocolVersion;

    /**
     * @var mixed
     */
    private mixed $serverInfo;

    /**
     * @param mixed $capabilities
     * @param string $protocolVersion
     * @param mixed $serverInfo
     */
    public function __construct(mixed $capabilities, string $protocolVersion, mixed $serverInfo)
    {
        $this->capabilities = $capabilities;
        $this->protocolVersion = $protocolVersion;
        $this->serverInfo = $serverInfo;
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
    public function getCapabilities() : mixed
    {
        return $this->capabilities;
    }

    /**
     * @return string|null
     */
    public function getInstructions() : ?string
    {
        return $this->instructions ?? null;
    }

    /**
     * @return string
     */
    public function getProtocolVersion() : string
    {
        return $this->protocolVersion;
    }

    /**
     * @return mixed
     */
    public function getServerInfo() : mixed
    {
        return $this->serverInfo;
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
     * @param mixed $capabilities
     * @return self
     */
    public function withCapabilities(mixed $capabilities) : self
    {
        $clone = clone $this;
        $clone->capabilities = $capabilities;

        return $clone;
    }

    /**
     * @param string $instructions
     * @return self
     */
    public function withInstructions(string $instructions) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($instructions, static::$schema['properties']['instructions']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->instructions = $instructions;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutInstructions() : self
    {
        $clone = clone $this;
        unset($clone->instructions);

        return $clone;
    }

    /**
     * @param string $protocolVersion
     * @return self
     */
    public function withProtocolVersion(string $protocolVersion) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($protocolVersion, static::$schema['properties']['protocolVersion']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->protocolVersion = $protocolVersion;

        return $clone;
    }

    /**
     * @param mixed $serverInfo
     * @return self
     */
    public function withServerInfo(mixed $serverInfo) : self
    {
        $clone = clone $this;
        $clone->serverInfo = $serverInfo;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return InitializeResult Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : InitializeResult
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $Meta = null;
        if (isset($input->{'_meta'})) {
            $Meta = (array)$input->{'_meta'};
        }
        $capabilities = $input->{'capabilities'};
        $instructions = null;
        if (isset($input->{'instructions'})) {
            $instructions = $input->{'instructions'};
        }
        $protocolVersion = $input->{'protocolVersion'};
        $serverInfo = $input->{'serverInfo'};

        $obj = new self($capabilities, $protocolVersion, $serverInfo);
        $obj->Meta = $Meta;
        $obj->instructions = $instructions;
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
        $output['capabilities'] = $this->capabilities;
        if (isset($this->instructions)) {
            $output['instructions'] = $this->instructions;
        }
        $output['protocolVersion'] = $this->protocolVersion;
        $output['serverInfo'] = $this->serverInfo;

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

