<?php

namespace ModelContextProtocol\Shared;

use Exception;
use RuntimeException;
use JsonSerializable;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Extra data for request handlers
 */
class RequestHandlerExtra {}

/**
 * Base Protocol class implementing MCP protocol framing
 */
abstract class Protocol 
{
    protected const IMPLEMENTATION_NAME = 'mcp-php';

    protected ?Transport $transport = null;
    protected array $requestHandlers = [];
    protected array $notificationHandlers = [];
    protected array $responseHandlers = [];
    protected array $progressHandlers = [];
    protected ?callable $fallbackRequestHandler = null;
    protected ?callable $fallbackNotificationHandler = null;

    public function __construct(
        protected ?ProtocolOptions $options = null
    ) {
        // Set default notification handlers
        $this->setNotificationHandler(
            Method::NOTIFICATIONS_PROGRESS,
            function($notification) {
                return $this->onProgress($notification);
            }
        );

        // Set default request handlers
        $this->setRequestHandler(
            Method::PING,
            function($request, $extra) {
                return new EmptyRequestResult();
            }
        );
    }

    /**
     * Connects to the given transport and starts listening for messages
     */
    public function connect(Transport $transport): void {
        $this->transport = $transport;

        $transport->onClose = function() {
            $this->doClose();
        };

        $transport->onError = function($error) {
            $this->onError($error);
        };

        $transport->onMessage = function($message) {
            if ($message instanceof JSONRPCResponse) {
                $this->onResponse($message, null);
            } elseif ($message instanceof JSONRPCRequest) {
                $this->onRequest($message);
            } elseif ($message instanceof JSONRPCNotification) {
                $this->onNotification($message);
            } elseif ($message instanceof JSONRPCError) {
                $this->onResponse(null, $message);
            }
        };

        $transport->start();
    }

    /**
     * Handles connection close
     */
    protected function doClose(): void {
        $this->responseHandlers = [];
        $this->progressHandlers = [];
        $this->transport = null;
        $this->onClose();

        $error = new McpError(ErrorCode::CONNECTION_CLOSED, "Connection closed");
        foreach ($this->responseHandlers as $handler) {
            $handler(null, $error);
        }
    }

    /**
     * Handles incoming notifications
     */
    protected function onNotification(JSONRPCNotification $notification): void {
        $handler = $this->notificationHandlers[$notification->method]
            ?? $this->fallbackNotificationHandler;

        if ($handler === null) {
            return;
        }

        try {
            $handler($notification);
        } catch (Exception $e) {
            $this->onError($e);
        }
    }

    /**
     * Handles incoming requests
     */
    protected function onRequest(JSONRPCRequest $request): void {

        $handler = $this->requestHandlers[$request->method] ?? $this->fallbackRequestHandler;

        if ($handler === null) {
            try {
                $this->transport?->send(new JSONRPCResponse(
                    $request->id,
                    null,
                    new JSONRPCError(
                        ErrorCode::METHOD_NOT_FOUND,
                        "Server does not support {$request->method}"
                    )
                ));
            } catch (Exception $e) {
                $this->onError($e);
            }
            return;
        }

        try {
            $result = $handler($request, new RequestHandlerExtra());

            $this->transport?->send(new JSONRPCResponse($request->id, $result));
        } catch (Exception $e) {

            $this->transport?->send(new JSONRPCResponse(
                $request->id,
                null,
                new JSONRPCError(
                    ErrorCode::INTERNAL_ERROR,
                    $e->getMessage() ?? "Internal error"
                )
            ));
        }
    }

    /**
     * Handles progress notifications
     */
    protected function onProgress(ProgressNotification $notification): void {
        $handler = $this->progressHandlers[$notification->progressToken] ?? null;
        if ($handler === null) {
            $error = new Exception(
                "Received progress notification for unknown token: " . json_encode($notification)
            );
            $this->onError($error);
            return;
        }

        $handler(new Progress($notification->progress, $notification->total));
    }

    /**
     * Handles responses to requests
     */
    protected function onResponse(?JSONRPCResponse $response, ?JSONRPCError $error): void {
        $messageId = $response?->id;
        $handler = $this->responseHandlers[$messageId] ?? null;

        if ($handler === null) {
            $this->onError(new Exception(
                "Received response for unknown message ID: " . json_encode($response)
            ));
            return;
        }

        unset($this->responseHandlers[$messageId]);
        unset($this->progressHandlers[$messageId]);

        if ($response !== null) {
            $handler($response, null);
        } else {
            if ($error === null) {
                throw new RuntimeException("Both response and error cannot be null");
            }
            $mcpError = new McpError(
                $error->code,
                $error->message,
                $error->data
            );
            $handler(null, $mcpError);
        }
    }

    /**
     * Sends a request and waits for response
     */
    public function request(Request $request, ?RequestOptions $options = null): RequestResult {

        if ($this->transport === null) {
            throw new RuntimeException("Not connected");
        }

        if ($this->options?->enforceStrictCapabilities) {
            $this->assertCapabilityForMethod($request->method);
        }

        $message = $request->toJSON();
        $messageId = $message->id;

        if ($options?->onProgress !== null) {
            $this->progressHandlers[$messageId] = $options->onProgress;
        }

        $promise = new Promise();

        $this->responseHandlers[$messageId] = function($response, $error) use ($promise) {
            if ($error !== null) {
                $promise->reject($error);
                return;
            }

            if ($response?->error !== null) {
                $promise->reject(new RuntimeException($response->error->toString()));
                return;
            }

            try {
                $promise->resolve($response->result);
            } catch (Exception $e) {
                $promise->reject($e);
            }
        };

        $timeout = $options?->timeout ?? ProtocolOptions::DEFAULT_REQUEST_TIMEOUT;

        try {
            $this->transport->send($message);
            return $promise->wait($timeout);
        } catch (Exception $e) {
            // Send cancellation notification
            $notification = new CancelledNotification(
                $messageId,
                $e->getMessage() ?? "Unknown"
            );

            $this->transport->send($notification->toJSON());

            unset($this->responseHandlers[$messageId]);
            unset($this->progressHandlers[$messageId]);

            throw $e;
        }
    }

    /**
     * Sends a notification
     */
    public function notification(Notification $notification): void {
        if ($this->transport === null) {
            throw new RuntimeException("Not connected");
        }

        $this->assertNotificationCapability($notification->method);

        $message = new JSONRPCNotification(
            $notification->method,
            json_decode(json_encode($notification), true)
        );

        $this->transport->send($message);
    }

    /**
     * Sets a request handler for a specific method
     */
    public function setRequestHandler(string $method, callable $handler): void {
        $this->assertRequestHandlerCapability($method);
        $this->requestHandlers[$method] = $handler;
    }

    /**
     * Removes a request handler
     */
    public function removeRequestHandler(string $method): void {
        unset($this->requestHandlers[$method]);
    }

    /**
     * Sets a notification handler for a specific method
     */
    public function setNotificationHandler(string $method, callable $handler): void {
        $this->notificationHandlers[$method] = $handler;
    }

    /**
     * Removes a notification handler
     */
    public function removeNotificationHandler(string $method): void {
        unset($this->notificationHandlers[$method]);
    }

    /**
     * Closes the connection
     */
    public function close(): void {
        $this->transport?->close();
    }

    /**
     * Called when connection is closed
     */
    public function onClose(): void {}

    /**
     * Called when an error occurs
     */
    public function onError(Exception $error): void {}

    /**
     * Abstract methods that must be implemented by subclasses
     */
    abstract protected function assertCapabilityForMethod(string $method): void;
    abstract protected function assertNotificationCapability(string $method): void;
    abstract public function assertRequestHandlerCapability(string $method): void;
}
