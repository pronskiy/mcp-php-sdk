<?php

namespace ModelContextProtocol\Shared;

class RequestOptions {
    public function __construct(
        public ?callable $onProgress = null,
        public int $timeout = ProtocolOptions::DEFAULT_REQUEST_TIMEOUT
    ) {}
}
