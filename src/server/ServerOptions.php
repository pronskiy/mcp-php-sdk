<?php

namespace ModelContextProtocol\Server;

use ModelContextProtocol\Shared\Protocol;
use ModelContextProtocol\Shared\ProtocolOptions;
use ModelContextProtocol\Shared\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ModelContextProtocol\Types\ServerCapabilities;

/**
 * Configuration options for the MCP server.
 */
class ServerOptions extends ProtocolOptions
{
    public ServerCapabilities $capabilities;
    
    public ?string $instructions = '';

    public function __construct(ServerCapabilities $capabilities, bool $enforceStrictCapabilities = true)
    {
        parent::__construct($enforceStrictCapabilities);
        $this->capabilities = $capabilities;
    }
}
