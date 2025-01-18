<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ClientCapabilities
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'Capabilities a client may support. Known capabilities are defined here, in this schema, but this is not a closed set: any client can define its own, additional capabilities.',
        'properties' => [
            'experimental' => [
                'additionalProperties' => [
                    'additionalProperties' => true,
                    'properties' => [
                        
                    ],
                    'type' => 'object',
                ],
                'description' => 'Experimental, non-standard capabilities that the client supports.',
                'type' => 'object',
            ],
            'roots' => [
                'description' => 'Present if the client supports listing roots.',
                'properties' => [
                    'listChanged' => [
                        'description' => 'Whether the client supports notifications for changes to the roots list.',
                        'type' => 'boolean',
                    ],
                ],
                'type' => 'object',
            ],
            'sampling' => [
                'additionalProperties' => true,
                'description' => 'Present if the client supports sampling from an LLM.',
                'properties' => [
                    
                ],
                'type' => 'object',
            ],
        ],
        'type' => 'object',
    ];

    /**
     * Experimental, non-standard capabilities that the client supports.
     *
     * @var ClientCapabilitiesExperimentalItem[]|null
     */
    private ?array $experimental = null;

    /**
     * Present if the client supports listing roots.
     *
     * @var ClientCapabilitiesRoots|null
     */
    private ?ClientCapabilitiesRoots $roots = null;

    /**
     * Present if the client supports sampling from an LLM.
     *
     * @var ClientCapabilitiesSampling|null
     */
    private ?ClientCapabilitiesSampling $sampling = null;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return ClientCapabilitiesExperimentalItem[]|null
     */
    public function getExperimental() : ?array
    {
        return $this->experimental ?? null;
    }

    /**
     * @return ClientCapabilitiesRoots|null
     */
    public function getRoots() : ?ClientCapabilitiesRoots
    {
        return $this->roots ?? null;
    }

    /**
     * @return ClientCapabilitiesSampling|null
     */
    public function getSampling() : ?ClientCapabilitiesSampling
    {
        return $this->sampling ?? null;
    }

    /**
     * @param ClientCapabilitiesExperimentalItem[] $experimental
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
     * @param ClientCapabilitiesRoots $roots
     * @return self
     */
    public function withRoots(ClientCapabilitiesRoots $roots) : self
    {
        $clone = clone $this;
        $clone->roots = $roots;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutRoots() : self
    {
        $clone = clone $this;
        unset($clone->roots);

        return $clone;
    }

    /**
     * @param ClientCapabilitiesSampling $sampling
     * @return self
     */
    public function withSampling(ClientCapabilitiesSampling $sampling) : self
    {
        $clone = clone $this;
        $clone->sampling = $sampling;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutSampling() : self
    {
        $clone = clone $this;
        unset($clone->sampling);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return ClientCapabilities Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ClientCapabilities
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $experimental = null;
        if (isset($input->{'experimental'})) {
            $experimental = array_map(fn (array|object $i): ClientCapabilitiesExperimentalItem => ClientCapabilitiesExperimentalItem::buildFromInput($i, validate: $validate), $input->{'experimental'});
        }
        $roots = null;
        if (isset($input->{'roots'})) {
            $roots = ClientCapabilitiesRoots::buildFromInput($input->{'roots'}, validate: $validate);
        }
        $sampling = null;
        if (isset($input->{'sampling'})) {
            $sampling = ClientCapabilitiesSampling::buildFromInput($input->{'sampling'}, validate: $validate);
        }

        $obj = new self();
        $obj->experimental = $experimental;
        $obj->roots = $roots;
        $obj->sampling = $sampling;
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
            $output['experimental'] = array_map(fn (ClientCapabilitiesExperimentalItem $i) => $i->toJson(), $this->experimental);
        }
        if (isset($this->roots)) {
            $output['roots'] = ($this->roots)->toJson();
        }
        if (isset($this->sampling)) {
            $output['sampling'] = ($this->sampling)->toJson();
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
            $this->experimental = array_map(fn (ClientCapabilitiesExperimentalItem $i) => clone $i, $this->experimental);
        }
        if (isset($this->roots)) {
            $this->roots = clone $this->roots;
        }
        if (isset($this->sampling)) {
            $this->sampling = clone $this->sampling;
        }
    }
}

