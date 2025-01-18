<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class ServerCapabilitiesTools
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'Present if the server offers any tools to call.',
        'properties' => [
            'listChanged' => [
                'description' => 'Whether this server supports notifications for changes to the tool list.',
                'type' => 'boolean',
            ],
        ],
        'type' => 'object',
    ];

    /**
     * Whether this server supports notifications for changes to the tool list.
     *
     * @var bool|null
     */
    private ?bool $listChanged = null;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return bool|null
     */
    public function getListChanged() : ?bool
    {
        return $this->listChanged ?? null;
    }

    /**
     * @param bool $listChanged
     * @return self
     */
    public function withListChanged(bool $listChanged) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($listChanged, static::$schema['properties']['listChanged']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->listChanged = $listChanged;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutListChanged() : self
    {
        $clone = clone $this;
        unset($clone->listChanged);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return ServerCapabilitiesTools Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : ServerCapabilitiesTools
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $listChanged = null;
        if (isset($input->{'listChanged'})) {
            $listChanged = (bool)($input->{'listChanged'});
        }

        $obj = new self();
        $obj->listChanged = $listChanged;
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
        if (isset($this->listChanged)) {
            $output['listChanged'] = $this->listChanged;
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
    }
}

