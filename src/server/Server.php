<?php

namespace ModelContextProtocol\Server;

const LATEST_PROTOCOL_VERSION = "2024-11-05";
const SUPPORTED_PROTOCOL_VERSIONS = [
    LATEST_PROTOCOL_VERSION,
    "2024-10-07",
];
const JSONRPC_VERSION = "2.0";

use ModelContextProtocol\Shared\Protocol;
use ModelContextProtocol\Shared\RequestOptions;
use ModelContextProtocol\Types\ClientCapabilities;
use ModelContextProtocol\Types\Implementation;
use ModelContextProtocol\Types\ServerCapabilities;
use ModelContextProtocol\Types\InitializeRequest;
use ModelContextProtocol\Types\InitializeResult;
use ModelContextProtocol\Types\InitializedNotification;
use ModelContextProtocol\Types\CreateMessageRequest;
use ModelContextProtocol\Types\ListRootsRequest;
use ModelContextProtocol\Types\LoggingMessageNotification;
use ModelContextProtocol\Types\ResourceUpdatedNotification;
use React\Promise\PromiseInterface;

class Server extends Protocol
{
    private ?ClientCapabilities $clientCapabilities = null;
    private ?Implementation $clientVersion = null;
    private ServerCapabilities $capabilities;
    private ?string $instructions = '';
    private Implementation $serverInfo;

    /**
     * Callback for when initialization has fully completed.
     */
    public $oninitialized;

    public function __construct(Implementation $serverInfo, ServerOptions $options)
    {
        parent::__construct($options);
        $this->serverInfo = $serverInfo;
        $this->capabilities = $options->capabilities;
        $this->instructions = $options->instructions;

        $this->setRequestHandler('initialize', [$this, 'onInitialize']);
        $this->setNotificationHandler('initialized', function() {
            if ($this->oninitialized) {
                call_user_func($this->oninitialized);
            }
        });
    }

    public function setRequestHandler()
    {
        
    }

    public function setNotificationHandler()
    {
        
    }

    protected function assertCapabilityForMethod(string $method): void
    {
        switch ($method) {
            case 'sampling/createMessage':
                if (!$this->clientCapabilities?->sampling) {
                    throw new \Exception("Client does not support sampling (required for {$method})");
                }
                break;

            case 'roots/list':
                if (!$this->clientCapabilities?->roots) {
                    throw new \Exception("Client does not support listing roots (required for {$method})");
                }
                break;

            case 'ping':
                // No specific capability required for ping
                break;
        }
    }

    protected function assertNotificationCapability(string $method): void
    {
        switch ($method) {
            case 'notifications/message':
                if (!$this->capabilities->logging) {
                    throw new \Exception("Server does not support logging (required for {$method})");
                }
                break;

            case 'notifications/resources/updated':
            case 'notifications/resources/list_changed':
                if (!$this->capabilities->resources) {
                    throw new \Exception("Server does not support notifying about resources (required for {$method})");
                }
                break;

            case 'notifications/tools/list_changed':
                if (!$this->capabilities->tools) {
                    throw new \Exception("Server does not support notifying of tool list changes (required for {$method})");
                }
                break;

            case 'notifications/prompts/list_changed':
                if (!$this->capabilities->prompts) {
                    throw new \Exception("Server does not support notifying of prompt list changes (required for {$method})");
                }
                break;

            case 'notifications/cancelled':
            case 'notifications/progress':
                // These notifications are always allowed
                break;
        }
    }

    protected function assertRequestHandlerCapability(string $method): void
    {
        switch ($method) {
            case 'sampling/createMessage':
                if (!$this->capabilities->sampling) {
                    throw new \Exception("Server does not support sampling (required for {$method})");
                }
                break;

            case 'logging/setLevel':
                if (!$this->capabilities->logging) {
                    throw new \Exception("Server does not support logging (required for {$method})");
                }
                break;

            case 'prompts/get':
            case 'prompts/list':
                if (!$this->capabilities->prompts) {
                    throw new \Exception("Server does not support prompts (required for {$method})");
                }
                break;

            case 'resources/list':
            case 'resources/templates/list':
            case 'resources/read':
                if (!$this->capabilities->resources) {
                    throw new \Exception("Server does not support resources (required for {$method})");
                }
                break;

            case 'tools/call':
            case 'tools/list':
                if (!$this->capabilities->tools) {
                    throw new \Exception("Server does not support tools (required for {$method})");
                }
                break;

            case 'ping':
            case 'initialize':
                // No specific capability required for these methods
                break;
        }
    }

    private function onInitialize(InitializeRequest $request): InitializeResult
    {
        $requestedVersion = $request->params->protocolVersion;

        $this->clientCapabilities = $request->params->capabilities;
        $this->clientVersion = $request->params->clientInfo;

        return new InitializeResult([
            'protocolVersion' => in_array($requestedVersion, SUPPORTED_PROTOCOL_VERSIONS) 
                ? $requestedVersion 
                : LATEST_PROTOCOL_VERSION,
            'capabilities' => $this->getCapabilities(),
            'serverInfo' => $this->serverInfo,
            'instructions' => $this->instructions,
        ]);
    }

    public function getClientCapabilities(): ?ClientCapabilities
    {
        return $this->clientCapabilities;
    }

    public function getClientVersion(): ?Implementation
    {
        return $this->clientVersion;
    }

    private function getCapabilities(): ServerCapabilities
    {
        return $this->capabilities;
    }

    public function ping(): PromiseInterface
    {
        return $this->request(['method' => 'ping']);
    }

    public function createMessage(array $params, ?RequestOptions $options = null): PromiseInterface
    {
        return $this->request([
            'method' => 'sampling/createMessage',
            'params' => $params
        ], $options);
    }

    public function listRoots(?array $params = null, ?RequestOptions $options = null): PromiseInterface
    {
        return $this->request([
            'method' => 'roots/list',
            'params' => $params
        ], $options);
    }

    public function sendLoggingMessage(array $params): void
    {
        $this->notification([
            'method' => 'notifications/message',
            'params' => $params
        ]);
    }

    public function sendResourceUpdated(array $params): void
    {
        $this->notification([
            'method' => 'notifications/resources/updated',
            'params' => $params
        ]);
    }

    public function sendResourceListChanged(): void
    {
        $this->notification([
            'method' => 'notifications/resources/list_changed'
        ]);
    }

    public function sendToolListChanged(): void
    {
        $this->notification([
            'method' => 'notifications/tools/list_changed'
        ]);
    }

    public function sendPromptListChanged(): void
    {
        $this->notification([
            'method' => 'notifications/prompts/list_changed'
        ]);
    }
}
