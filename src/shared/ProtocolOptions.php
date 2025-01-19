<?php

namespace ModelContextProtocol\Shared;

class ProtocolOptions {
    public function __construct(
        public bool $enforceStrictCapabilities = false,
    ) {}

    public const DEFAULT_REQUEST_TIMEOUT = 60000; // milliseconds
}

