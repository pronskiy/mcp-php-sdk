<?php

namespace ModelContextProtocol\Shared;

use ModelContextProtocol\Types\JSONRPCMessage;
use ModelContextProtocol\Types\JSONRPCRequest;
use ModelContextProtocol\Types\JSONRPCResponse;
use ModelContextProtocol\Types\JSONRPCNotification;
use ModelContextProtocol\Types\JSONRPCError;
use ModelContextProtocol\Types\Request;
use ModelContextProtocol\Types\Result;
use ModelContextProtocol\Types\Notification;
use React\Promise\PromiseInterface;

/**
 * Implements MCP protocol framing on top of a pluggable transport, including
 * features like request/response linking, notifications, and progress.
 */
abstract class Protocol
{
    /**
     * Invalid JSON was received by the server.
     * An error occurred on the server while parsing the JSON text.
     */
    private const ERROR_PARSE = -32700;

    /**
     * The JSON sent is not a valid Request object.
     */
    private const ERROR_INVALID_REQUEST = -32600;

    /**
     * The method does not exist / is not available.
     */
    private const ERROR_METHOD_NOT_FOUND = -32601;

    /**
     * Invalid method parameter(s).
     */
    private const ERROR_INVALID_PARAMS = -32602;

    /**
     * Internal JSON-RPC error.
     */
    private const ERROR_INTERNAL = -32603;

    /**
     * Request timeout error.
     */
    private const ERROR_REQUEST_TIMEOUT = -32000;

    /**
     * Request was cancelled.
     */
    private const ERROR_REQUEST_CANCELLED = -32001;
    private ?Transport $transport = null;
    private int $requestMessageId = 0;
    /** @var array<string, callable(JSONRPCRequest): mixed> */
    private array $requestHandlers = [];
    /** @var array<int, array{timer: int, controller: mixed}> */
    private array $requestHandlerAbortControllers = [];
    /** @var array<string, callable(JSONRPCNotification): void> */
    private array $notificationHandlers = [];
    /** @var array<int, callable(JSONRPCResponse|\Throwable): void> */
    private array $responseHandlers = [];
    /** @var array<int, callable(mixed): void> */
    private array $progressHandlers = [];

    public function __construct(
        private ?ProtocolOptions $options = null
    ) {
        $this->options = $options ?? new ProtocolOptions();
    }

    /**
     * Sets the transport to use for this protocol instance.
     */
    public function setTransport(Transport $transport): void
    {
        $this->transport = $transport;
        $transport->setOnMessage(\Closure::fromCallable([$this, 'handleMessage']));
        $transport->setOnError(\Closure::fromCallable([$this, 'handleError']));
        $transport->setOnClose(\Closure::fromCallable([$this, 'handleClose']));
    }

    /**
     * Starts the protocol.
     */
    public function start(): void
    {
        if (!$this->transport) {
            throw new \RuntimeException('No transport set');
        }
        $this->transport->start();
    }

    /**
     * Closes the protocol.
     */
    public function close(): void
    {
        if ($this->transport) {
            $this->transport->close();
        }
    }

    /**
     * Cleans up request handlers and timers for a given message ID
     */
    private function cleanupRequest(int $messageId): void
    {
        unset($this->responseHandlers[$messageId]);
        unset($this->progressHandlers[$messageId]);
        unset($this->requestHandlerAbortControllers[$messageId]);
    }

    protected function handleMessage(JSONRPCMessage $message): void
    {
        // Check for timeouts
        $currentTime = time();
        foreach ($this->requestHandlerAbortControllers as $messageId => $data) {
            if ($currentTime >= $data['timer']) {
                $error = new JSONRPCError(
                    self::ERROR_REQUEST_TIMEOUT,
                    'Request timeout',
                    ['timeout' => $data['timer']]
                );
                if (isset($this->responseHandlers[$messageId])) {
                    $this->responseHandlers[$messageId]($error);
                }
                $this->cleanupRequest($messageId);
            }
        }

        if (!property_exists($message, 'method')) {
            $this->handleResponse($message);
        } elseif (property_exists($message, 'id')) {
            $this->handleRequest($message);
        } else {
            $this->handleNotification($message);
        }
    }

    private function handleResponse(JSONRPCResponse $response): void
    {
        if (!isset($this->responseHandlers[$response->id])) {
            return;
        }

        $handler = $this->responseHandlers[$response->id];
        unset($this->responseHandlers[$response->id]);
        unset($this->progressHandlers[$response->id]);

        $handler($response);
    }

//    private function handleRequest(JSONRPCRequest $request): void
    private function handleRequest(JSONRPCMessage $request): void
    {
        $handler = $this->requestHandlers[$request->method] ?? null;

        if ($handler === null) {
            // If no handler is registered for this method, respond with method not found error
            $error = new JSONRPCError(
                self::ERROR_METHOD_NOT_FOUND,
                "Method not found",
//                $request->method
            );
            $this->sendError($request->id, $error);
            return;
        }

        try {
            $result = $handler($request);
            $this->sendResult($request->id, $result);
        } catch (\Throwable $e) {
            $error = new JSONRPCError(
                self::ERROR_INTERNAL,
                $e->getMessage(),
                ["trace" => $e->getTraceAsString()]
            );
            $this->sendError($request->id, $error);
        }
    }

    private function handleNotification(JSONRPCNotification $notification): void
    {
        $handler = $this->notificationHandlers[$notification->method] ?? null;

        if ($handler === null) {
            return; // Notifications without handlers are silently ignored
        }

        try {
            $handler($notification);
        } catch (\Throwable $e) {
            // Log error but don't send response as this is a notification
            if ($this->options->enforceStrictCapabilities) {
                throw $e;
            }
        }
    }

    private function sendResult($id, $result): void
    {
        if (!$this->transport) {
            throw new \RuntimeException('No transport set');
        }

        $response = new JSONRPCResponse();
        $response->id = $id;
        $response->result = $result;

        $this->transport->send($response);
    }

    private function sendError($id, JSONRPCError $error): void
    {
        if (!$this->transport) {
            throw new \RuntimeException('No transport set');
        }

        $response = new JSONRPCMessage(id: $id, result: new Result());

        $this->transport->send($response);
    }

    /**
     * Sends a request and waits for a response.
     */
    protected function request(string $method, mixed $params = null, ?RequestOptions $options = null): PromiseInterface
    {
        if (!$this->transport) {
            throw new \RuntimeException('Not connected');
        }

        if ($this->options->enforceStrictCapabilities) {
            $this->assertCapabilityForMethod($method);
        }

        $messageId = $this->requestMessageId++;
        $request = new JSONRPCRequest();
        $request->jsonrpc = '2.0';
        $request->method = $method;
        $request->params = $params;
        $request->id = $messageId;

        if (isset($options) && isset($options->onProgress)) {
            $this->progressHandlers[$messageId] = $options->onProgress;
            $request->params = array_merge(
                (array)$params,
                ['_meta' => ['progressToken' => $messageId]]
            );
        }

        return new \React\Promise\Promise(function ($resolve, $reject) use ($messageId, $request, $options) {
            $timeout = $options?->timeout ?? ProtocolOptions::DEFAULT_REQUEST_TIMEOUT;

            // Set up timeout
            $timer = null;
            if ($timeout > 0) {
                $timer = time() + ($timeout / 1000); // Convert to seconds
                $this->requestHandlerAbortControllers[$messageId] = [
                    'timer' => $timer,
                    'controller' => null
                ];
            }

            $this->responseHandlers[$messageId] = function ($response) use ($resolve, $reject, $messageId, $timer) {
                // Clear timeout
                if ($timer !== null) {
                    unset($this->requestHandlerAbortControllers[$messageId]);
                }

                if ($response instanceof \Throwable) {
                    $reject($response);
                    return;
                }

                try {
                    $resolve($response->result);
                } catch (\Throwable $e) {
                    $reject($e);
                }
            };

            try {
                $this->transport->send($request);
            } catch (\Throwable $e) {
                $this->cleanupRequest($messageId);
                $reject($e);
            }
        });
    }

    /**
     * Sends a notification (one-way message that does not expect a response).
     */
    protected function notify(string $method, mixed $params = null): void
    {
        if (!$this->transport) {
            throw new \RuntimeException('Not connected');
        }

        $this->assertNotificationCapability($method);

        $notification = new JSONRPCNotification();
        $notification->jsonrpc = '2.0';
        $notification->method = $method;
        $notification->params = $params;

        $this->transport->send($notification);
    }

    /**
     * Asserts that the remote side supports the given method.
     */
    abstract protected function assertCapabilityForMethod(string $method): void;

    /**
     * Asserts that the local side supports handling the given notification.
     */
    abstract protected function assertNotificationCapability(string $method): void;

    /**
     * Asserts that the local side supports handling the given request.
     */
    abstract protected function assertRequestHandlerCapability(string $method): void;

    /**
     * Handles transport errors.
     */
    protected function handleError(\Throwable $error): void
    {
        // Notify all pending requests about the error
        foreach ($this->responseHandlers as $handler) {
            $handler($error);
        }
        $this->responseHandlers = [];
        $this->progressHandlers = [];
    }

    /**
     * Handles transport close.
     */
    protected function handleClose(): void
    {
        $error = new \RuntimeException('Connection closed');
        $this->handleError($error);
        $this->transport = null;
    }

//    /**
//     * Sends a request to the remote end and waits for a response.
//     */
//    protected function request(string $method, $params = null, ?RequestOptions $options = null)
//    {
//        // Implementation will follow
//    }

//    /**
//     * Sends a notification to the remote end.
//     */
//    protected function notify(string $method, $params = null): void
//    {
//        // Implementation will follow
//    }

    /**
     * Registers a handler for incoming requests.
     */
    protected function onRequest(string $method, callable $handler): void
    {
        $this->requestHandlers[$method] = $handler;
    }

    /**
     * Registers a handler for incoming notifications.
     */
    protected function onNotification(string $method, callable $handler): void
    {
        $this->notificationHandlers[$method] = $handler;
    }
}
