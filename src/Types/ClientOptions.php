<?php

namespace ModelContextProtocol\Types;

class ClientOptions {
    private array $capabilities;

    public function __construct(array $options) {
        $this->capabilities = $options['capabilities'] ?? [];
    }

    public function getCapabilities(): array {
        return $this->capabilities;
    }
}
