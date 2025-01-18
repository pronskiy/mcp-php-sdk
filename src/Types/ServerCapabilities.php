<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ServerCapabilities
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'Capabilities that a server may support. Known capabilities are defined here, in this schema, but this is not a closed set: any server can define its own, additional capabilities.',
        'properties' => [
            'experimental' => [
                'additionalProperties' => [
                    'additionalProperties' => true,
                    'properties' => [
                        
                    ],
                    'type' => 'object',
                ],
                'description' => 'Experimental, non-standard capabilities that the server supports.',
                'type' => 'object',
            ],
            'logging' => [
                'additionalProperties' => true,
                'description' => 'Present if the server supports sending log messages to the client.',
                'properties' => [
                    
                ],
                'type' => 'object',
            ],
            'prompts' => [
                'description' => 'Present if the server offers any prompt templates.',
                'properties' => [
                    'listChanged' => [
                        'description' => 'Whether this server supports notifications for changes to the prompt list.',
                        'type' => 'boolean',
                    ],
                ],
                'type' => 'object',
            ],
            'resources' => [
                'description' => 'Present if the server offers any resources to read.',
                'properties' => [
                    'listChanged' => [
                        'description' => 'Whether this server supports notifications for changes to the resource list.',
                        'type' => 'boolean',
                    ],
                    'subscribe' => [
                        'description' => 'Whether this server supports subscribing to resource updates.',
                        'type' => 'boolean',
                    ],
                ],
                'type' => 'object',
            ],
            'tools' => [
                'description' => 'Present if the server offers any tools to call.',
                'properties' => [
                    'listChanged' => [
                        'description' => 'Whether this server supports notifications for changes to the tool list.',
                        'type' => 'boolean',
                    ],
                ],
                'type' => 'object',
            ],
        ],
        'type' => 'object',
    ];

    /**
     * Experimental, non-standard capabilities that the server supports.
     *
     * @var ServerCapabilitiesExperimentalItem[]|null
     */
    private ?array $experimental = null;

    /**
     * Present if the server supports sending log messages to the client.
     *
     * @var ServerCapabilitiesLogging|null
     */
    private ?ServerCapabilitiesLogging $logging = null;

    /**
     * Present if the server offers any prompt templates.
     *
     * @var ServerCapabilitiesPrompts|null
     */
    private ?ServerCapabilitiesPrompts $prompts = null;

    /**
     * Present if the server offers any resources to read.
     *
     * @var ServerCapabilitiesResources|null
     */
    private ?ServerCapabilitiesResources $resources = null;

    /**
     * Present if the server offers any tools to call.
     *
     * @var ServerCapabilitiesTools|null
     */
    private ?ServerCapabilitiesTools $tools = null;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return ServerCapabilitiesExperimentalItem[]|null
     */
    public function getExperimental() : ?array
    {
        return $this->experimental ?? null;
    }

    /**
     * @return ServerCapabilitiesLogging|null
     */
    public function getLogging() : ?ServerCapabilitiesLogging
    {
        return $this->logging ?? null;
    }

    /**
     * @return ServerCapabilitiesPrompts|null
     */
    public function getPrompts() : ?ServerCapabilitiesPrompts
    {
        return $this->prompts ?? null;
    }

    /**
     * @return ServerCapabilitiesResources|null
     */
    public function getResources() : ?ServerCapabilitiesResources
    {
        return $this->resources ?? null;
    }

    /**
     * @return ServerCapabilitiesTools|null
     */
    public function getTools() : ?ServerCapabilitiesTools
    {
        return $this->tools ?? null;
    }

    /**
     * @param ServerCapabilitiesExperimentalItem[] $experimental
     * @return self
     */
    public function withExperimental(array $experimental) : self
    {
        $clone = clone $this;
        $clone->experimental = $experimental;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutExperimental() : self
    {
        $clone = clone $this;
        unset($clone->experimental);

        return $clone;
    }

    /**
     * @param ServerCapabilitiesLogging $logging
     * @return self
     */
    public function withLogging(ServerCapabilitiesLogging $logging) : self
    {
        $clone = clone $this;
        $clone->logging = $logging;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutLogging() : self
    {
        $clone = clone $this;
        unset($clone->logging);

        return $clone;
    }

    /**
     * @param ServerCapabilitiesPrompts $prompts
     * @return self
     */
    public function withPrompts(ServerCapabilitiesPrompts $prompts) : self
    {
        $clone = clone $this;
        $clone->prompts = $prompts;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutPrompts() : self
    {
        $clone = clone $this;
        unset($clone->prompts);

        return $clone;
    }

    /**
     * @param ServerCapabilitiesResources $resources
     * @return self
     */
    public function withResources(ServerCapabilitiesResources $resources) : self
    {
        $clone = clone $this;
        $clone->resources = $resources;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutResources() : self
    {
        $clone = clone $this;
        unset($clone->resources);

        return $clone;
    }

    /**
     * @param ServerCapabilitiesTools $tools
     * @return self
     */
    public function withTools(ServerCapabilitiesTools $tools) : self
    {
        $clone = clone $this;
        $clone->tools = $tools;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutTools() : self
    {
        $clone = clone $this;
        unset($clone->tools);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return ServerCapabilities Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ServerCapabilities
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $experimental = null;
        if (isset($input->{'experimental'})) {
            $experimental = array_map(fn (array|object $i): ServerCapabilitiesExperimentalItem => ServerCapabilitiesExperimentalItem::buildFromInput($i, validate: $validate), $input->{'experimental'});
        }
        $logging = null;
        if (isset($input->{'logging'})) {
            $logging = ServerCapabilitiesLogging::buildFromInput($input->{'logging'}, validate: $validate);
        }
        $prompts = null;
        if (isset($input->{'prompts'})) {
            $prompts = ServerCapabilitiesPrompts::buildFromInput($input->{'prompts'}, validate: $validate);
        }
        $resources = null;
        if (isset($input->{'resources'})) {
            $resources = ServerCapabilitiesResources::buildFromInput($input->{'resources'}, validate: $validate);
        }
        $tools = null;
        if (isset($input->{'tools'})) {
            $tools = ServerCapabilitiesTools::buildFromInput($input->{'tools'}, validate: $validate);
        }

        $obj = new self();
        $obj->experimental = $experimental;
        $obj->logging = $logging;
        $obj->prompts = $prompts;
        $obj->resources = $resources;
        $obj->tools = $tools;
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
        if (isset($this->experimental)) {
            $output['experimental'] = array_map(fn (ServerCapabilitiesExperimentalItem $i) => $i->toJson(), $this->experimental);
        }
        if (isset($this->logging)) {
            $output['logging'] = ($this->logging)->toJson();
        }
        if (isset($this->prompts)) {
            $output['prompts'] = ($this->prompts)->toJson();
        }
        if (isset($this->resources)) {
            $output['resources'] = ($this->resources)->toJson();
        }
        if (isset($this->tools)) {
            $output['tools'] = ($this->tools)->toJson();
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
        if (isset($this->experimental)) {
            $this->experimental = array_map(fn (ServerCapabilitiesExperimentalItem $i) => clone $i, $this->experimental);
        }
        if (isset($this->logging)) {
            $this->logging = clone $this->logging;
        }
        if (isset($this->prompts)) {
            $this->prompts = clone $this->prompts;
        }
        if (isset($this->resources)) {
            $this->resources = clone $this->resources;
        }
        if (isset($this->tools)) {
            $this->tools = clone $this->tools;
        }
    }
}

