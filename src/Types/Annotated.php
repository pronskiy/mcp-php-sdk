<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

class Annotated
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'description' => 'Base for objects that include optional annotations for the client. The client can use annotations to inform how objects are used or displayed',
        'properties' => [
            'annotations' => [
                'properties' => [
                    'audience' => [
                        'description' => 'Describes who the intended customer of this object or data is.

It can include multiple entries to indicate content useful for multiple audiences (e.g., `["user", "assistant"]`).',
                        'items' => [
                            '$ref' => '#/definitions/Role',
                        ],
                        'type' => 'array',
                    ],
                    'priority' => [
                        'description' => 'Describes how important this data is for operating the server.

A value of 1 means "most important," and indicates that the data is
effectively required, while 0 means "least important," and indicates that
the data is entirely optional.',
                        'maximum' => 1,
                        'minimum' => 0,
                        'type' => 'number',
                    ],
                ],
                'type' => 'object',
            ],
        ],
        'type' => 'object',
    ];

    /**
     * @var AnnotatedAnnotations|null
     */
    private ?AnnotatedAnnotations $annotations = null;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return AnnotatedAnnotations|null
     */
    public function getAnnotations() : ?AnnotatedAnnotations
    {
        return $this->annotations ?? null;
    }

    /**
     * @param AnnotatedAnnotations $annotations
     * @return self
     */
    public function withAnnotations(AnnotatedAnnotations $annotations) : self
    {
        $clone = clone $this;
        $clone->annotations = $annotations;

        return $clone;
    }

    /**
     * @return self
     */
    public function withoutAnnotations() : self
    {
        $clone = clone $this;
        unset($clone->annotations);

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return Annotated Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : Annotated
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $annotations = null;
        if (isset($input->{'annotations'})) {
            $annotations = AnnotatedAnnotations::buildFromInput($input->{'annotations'}, validate: $validate);
        }

        $obj = new self();
        $obj->annotations = $annotations;
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
        if (isset($this->annotations)) {
            $output['annotations'] = ($this->annotations)->toJson();
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
        if (isset($this->annotations)) {
            $this->annotations = clone $this->annotations;
        }
    }
}

