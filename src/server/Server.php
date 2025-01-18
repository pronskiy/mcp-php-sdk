<?php

namespace ModelContextProtocol\Server;

//require_once __DIR__ . "/../types/types.php";

use ModelContextProtocol\Shared\Protocol;
use ModelContextProtocol\Shared\ProtocolOptions;
use ModelContextProtocol\Shared\RequestOptions;
use ModelContextProtocol\Types\ClientCapabilities;
use ModelContextProtocol\Types\Implementation;
use ModelContextProtocol\Types\ServerCapabilities;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * An MCP server on top of a pluggable transport.
 */
class Server extends Protocol
{
    private Implementation $serverInfo;
    public Closure $onCloseCallback;
    private ServerCapabilities $capabilities;
    private array $tools = [];
    private array $prompts = [];
    private array $resources = [];
    private ?ClientCapabilities $clientCapabilities = null;
    private ?Implementation $clientVersion = null;
    public ?Closure $onInitialized = null;
    private ?string $instructions;

    public function __construct(
        ServerOptions $options,
        Implementation $serverInfo,
//        ?callable $onCloseCallback = null,
    ) {
        parent::__construct($options);
        $this->serverInfo = $serverInfo;
//        $this->onCloseCallback = $onCloseCallback;
        $this->capabilities = $options->capabilities;
        $this->instructions = $options->instructions;

        $this->initializeHandlers();
    }

    private function initializeHandlers(): void
    {
        // Core protocol handlers
        $this->setRequestHandler(Method::DEFINED_INITIALIZE, function ($request) {
            return $this->handleInitialize($request);
        });

        $this->setNotificationHandler(Method::DEFINED_NOTIFICATIONS_INITIALIZED, function () {
            if ($this->onInitialized) {
                call_user_func($this->onInitialized);
            }
        });

        if ($this->capabilities->tools !== null) {
            $this->setRequestHandler(Method::DEFINED_TOOLS_LIST, function () {
                return $this->handleListTools();
            });
            $this->setRequestHandler(Method::DEFINED_TOOLS_CALL, function ($request) {
                return $this->handleCallTool($request);
            });
        }

        if ($this->capabilities->prompts !== null) {
            $this->setRequestHandler(Method::DEFINED_PROMPTS_LIST, function () {
                return $this->handleListPrompts();
            });
            $this->setRequestHandler(Method::DEFINED_PROMPTS_GET, function ($request) {
                return $this->handleGetPrompt($request);
            });
        }

        if ($this->capabilities->resources !== null) {
            $this->setRequestHandler(Method::DEFINED_RESOURCES_LIST, function () {
                return $this->handleListResources();
            });
            $this->setRequestHandler(Method::DEFINED_RESOURCES_READ, function ($request) {
                return $this->handleReadResource($request);
            });
            $this->setRequestHandler(Method::DEFINED_RESOURCES_TEMPLATES_LIST, function () {
                return $this->handleListResourceTemplates();
            });
        }
    }

    public function onClose(): void
    {
        if ($this->onCloseCallback) {
            call_user_func($this->onCloseCallback);
        }
    }

    public function addTool(string $name, string $description, array $inputSchema, callable $handler): void
    {
        if ($this->capabilities->tools === null) {
            throw new \RuntimeException("Server does not support tools capability");
        }

        $this->tools[$name] = new RegisteredTool($name, $description, $inputSchema, $handler);
    }

    public function addPrompt(
        string $name,
        ?string $description,
        ?array $arguments,
        callable $promptProvider
    ): void {
        if ($this->capabilities->prompts === null) {
            throw new \RuntimeException("Server does not support prompts capability");
        }

        $this->prompts[$name] = new RegisteredPrompt($name, $description, $arguments, $promptProvider);
    }

    public function addResource(
        string $uri,
        string $name,
        string $description,
        string $mimeType,
        callable $readHandler
    ): void {
        if ($this->capabilities->resources === null) {
            throw new \RuntimeException("Server does not support resources capability.");
        }

        $this->resources[$uri] = new RegisteredResource($uri, $name, $description, $mimeType, $readHandler);
    }

    private function handleInitialize($request)
    {
        $this->clientCapabilities = $request['capabilities'] ?? null;
        $this->clientVersion = $request['clientInfo'] ?? null;

        $requestedVersion = $request['protocolVersion'] ?? null;
        $protocolVersion = in_array($requestedVersion, Protocol::SUPPORTED_VERSIONS)
            ? $requestedVersion
            : Protocol::LATEST_VERSION;

        return [
            'protocolVersion' => $protocolVersion,
            'capabilities' => $this->capabilities,
            'serverInfo' => $this->serverInfo,
        ];
    }

    private function handleListTools()
    {
        return new ListToolsResult($this->tools);
    }

    private function handleCallTool($request)
    {
        $toolName = $request['name'] ?? null;

        if (!isset($this->tools[$toolName])) {
            throw new \InvalidArgumentException("Tool not found: {$toolName}");
        }

        $tool = $this->tools[$toolName];
        return call_user_func($tool->handler, $request);
    }

    private function handleListResources()
    {
        return new ListResourcesResult($this->resources);
    }

    private function handleReadResource($request)
    {
        $uri = $request['uri'] ?? null;

        if (!isset($this->resources[$uri])) {
            throw new \InvalidArgumentException("Resource not found: {$uri}");
        }

        $resource = $this->resources[$uri];
        return call_user_func($resource->readHandler, $request);
    }

    private function handleListPrompts()
    {
        return new ListPromptsResult($this->prompts);
    }

    private function handleGetPrompt($request)
    {
        $promptName = $request['name'] ?? null;

        if (!isset($this->prompts[$promptName])) {
            throw new \InvalidArgumentException("Prompt not found: {$promptName}");
        }

        $prompt = $this->prompts[$promptName];
        return call_user_func($prompt->messageProvider, $request);
    }

    protected function assertCapabilityForMethod(string $method): void
    {
        // TODO: Implement assertCapabilityForMethod() method.
    }

    protected function assertNotificationCapability(string $method): void
    {
        // TODO: Implement assertNotificationCapability() method.
    }

    public function assertRequestHandlerCapability(string $method): void
    {
        // TODO: Implement assertRequestHandlerCapability() method.
    }
}

/**
 * Wrapper classes for tools, prompts, and resources
 */
class RegisteredTool
{
    public string $name;
    public string $description;
    public array $inputSchema;
    public $handler;

    public function __construct(string $name, string $description, array $inputSchema, callable $handler)
    {
        $this->name = $name;
        $this->description = $description;
        $this->inputSchema = $inputSchema;
        $this->handler = $handler;
    }
}

class RegisteredPrompt
{
    public string $name;
    public ?string $description;
    public ?array $arguments;
    public $messageProvider;

    public function __construct(string $name, ?string $description, ?array $arguments, callable $messageProvider)
    {
        $this->name = $name;
        $this->description = $description;
        $this->arguments = $arguments;
        $this->messageProvider = $messageProvider;
    }
}

class RegisteredResource
{
    public string $uri;
    public string $name;
    public string $description;
    public string $mimeType;
    public $readHandler;

    public function __construct(string $uri, string $name, string $description, string $mimeType, callable $readHandler)
    {
        $this->uri = $uri;
        $this->name = $name;
        $this->description = $description;
        $this->mimeType = $mimeType;
        $this->readHandler = $readHandler;
    }
}
