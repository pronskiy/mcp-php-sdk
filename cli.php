<?php

use ModelContextProtocol\Client\Client;
use ModelContextProtocol\Client\SSEClientTransport;
use ModelContextProtocol\Client\StdioClientTransport;
use ModelContextProtocol\Client\WebSocketClientTransport;
use ModelContextProtocol\Server\SSEServerTransport;
use ModelContextProtocol\Server\StdioServerTransport;
use ModelContextProtocol\Server\Server;
use ModelContextProtocol\Server\ServerOptions;
use ModelContextProtocol\Types\Implementation;
use ModelContextProtocol\Types\ClientOptions;
use ModelContextProtocol\Types\ServerCapabilities;
use React\EventLoop\Factory;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\Server as SocketServer;
use React\Promise\Promise;

require 'vendor/autoload.php';

function runClient(string $urlOrCommand, array $args = []): void {
    $loop = \React\EventLoop\Factory::create();

    $client = new Client(
        new Implementation('mcp-php test client', '0.1.0'),
        new ClientOptions([
            'capabilities' => [
                'sampling' => []
            ]
        ]),
        $loop
    );

    $clientTransport = null;
    $url = parse_url($urlOrCommand);

    if ($url !== false && isset($url['scheme'])) {
        if (in_array($url['scheme'], ['http', 'https'])) {
            $clientTransport = new SSEClientTransport($urlOrCommand, $loop);
        } elseif (in_array($url['scheme'], ['ws', 'wss'])) {
            $clientTransport = new WebSocketClientTransport($urlOrCommand, $loop);
        }
    }

    if ($clientTransport === null) {
        $clientTransport = new StdioClientTransport([
            'command' => $urlOrCommand,
            'args' => $args
        ], $loop);
    }

    echo "Connected to server.\n";

    $client->connect($clientTransport);
    echo "Initialized.\n";

    $client->request(['method' => 'resources/list'])
        ->then(
            function($response) {
                echo "Request completed.\n";
            },
            function($error) {
                echo "Error: " . $error->getMessage() . "\n";
            }
        )
        ->then(function() use ($client) {
            $client->close();
            echo "Closed.\n";
        });

    $loop->run();
}

function runServer(?int $port = null): void {
    if ($port !== null) {
        $loop = Factory::create();
        $servers = [];

        $httpServer = new HttpServer(function ($request) use (&$servers) {
            $path = $request->getUri()->getPath();

            if ($path === '/sse') {
                echo "Got new SSE connection\n";

                $sessionId = uniqid();
                $transport = new SSEServerTransport('/message', $sessionId);
                $server = new Server(
                    new Implementation('mcp-php test server', '0.1.0'),
                    new ServerOptions([
                        'capabilities' => []
                    ])
                );

                $servers[$sessionId] = $server;
                $server->onCloseCallback = function () use (&$servers, $sessionId) {
                    echo "SSE connection closed\n";
                    unset($servers[$sessionId]);
                };

                $server->connect($transport);
                return new Response(200, [], "SSE connection established");
            }

            if ($path === '/message') {
                echo "Received message\n";

                $queryParams = $request->getQueryParams();
                $sessionId = $queryParams['sessionId'] ?? null;

                if (!isset($servers[$sessionId])) {
                    return new Response(404, [], "Session not found");
                }

                $transport = $servers[$sessionId]->transport;
                return $transport->handlePostMessage($request);
            }

            return new Response(404, [], "Not Found");
        });

        $socket = new SocketServer("0.0.0.0:$port", $loop);
        $httpServer->listen($socket);

        echo "Server running on http://localhost:$port/sse\n";
        $loop->run();
    } else {
        $server = new Server(
            new Implementation('mcp-php test server', '0.1.0'),
            new ServerOptions(new ServerCapabilities())
        );
        
        /*
         * [
                'capabilities' => [
                    'prompts' => [],
                    'resources' => [],
                    'tools' => [],
                    'logging' => []
                ]
            ]
         */

        $transport = new StdioServerTransport();
//        $server->connect($transport);
        $server->setTransport($transport);
        $server->start();

//        echo "Server running on stdio\n";
    }
}

$command = $argv[1] ?? null;

switch ($command) {
    case 'client':
        if (count($argv) < 3) {
            fwrite(STDERR, "Usage: client <server_url_or_command> [args...]\n");
            exit(1);
        }
        try {
            runClient($argv[2], array_slice($argv, 3));
        } catch (Exception $e) {
            fwrite(STDERR, $e->getMessage() . "\n");
            exit(1);
        }
        break;

    case 'server':
        $port = isset($argv[2]) ? intval($argv[2]) : null;
        try {
            runServer($port);
        } catch (Exception $e) {
            fwrite(STDERR, $e->getMessage() . "\n");
            exit(1);
        }
        break;

    default:
        fwrite(STDERR, "Unrecognized command: $command\n");
        exit(1);
}
