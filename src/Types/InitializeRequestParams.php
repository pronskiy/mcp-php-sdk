<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class InitializeRequestParams
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'properties' => [
            'capabilities' => [
                '$ref' => '#/definitions/ClientCapabilities',
            ],
            'clientInfo' => [
                '$ref' => '#/definitions/Implementation',
            ],
            'protocolVersion' => [
                'description' => 'The latest version of the Model Context Protocol that the client supports. The client MAY decide to support older versions as well.',
                'type' => 'string',
            ],
        ],
        'required' => [
            'capabilities',
            'clientInfo',
            'protocolVersion',
        ],
        'type' => 'object',
    ];

    /**
     * @var mixed
     */
    private mixed $capabilities;

    /**
     * @var mixed
     */
    private mixed $clientInfo;

    /**
     * The latest version of the Model Context Protocol that the client supports. The client MAY decide to support older versions as well.
     *
     * @var string
     */
    private string $protocolVersion;

    /**
     * @param mixed $capabilities
     * @param mixed $clientInfo
     * @param string $protocolVersion
     */
    public function __construct(mixed $capabilities, mixed $clientInfo, string $protocolVersion)
    {
        $this->capabilities = $capabilities;
        $this->clientInfo = $clientInfo;
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * @return mixed
     */
    public function getCapabilities() : mixed
    {
        return $this->capabilities;
    }

    /**
     * @return mixed
     */
    public function getClientInfo() : mixed
    {
        return $this->clientInfo;
    }

    /**
     * @return string
     */
    public function getProtocolVersion() : string
    {
        return $this->protocolVersion;
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
     * @param mixed $clientInfo
     * @return self
     */
    public function withClientInfo(mixed $clientInfo) : self
    {
        $clone = clone $this;
        $clone->clientInfo = $clientInfo;

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
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return InitializeRequestParams Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : InitializeRequestParams
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $capabilities = $input->{'capabilities'};
        $clientInfo = $input->{'clientInfo'};
        $protocolVersion = $input->{'protocolVersion'};

        $obj = new self($capabilities, $clientInfo, $protocolVersion);

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
        $output['capabilities'] = $this->capabilities;
        $output['clientInfo'] = $this->clientInfo;
        $output['protocolVersion'] = $this->protocolVersion;

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

