<?php

use React\EventLoop\Factory;
use React\Socket\Server as SocketServer;
use React\Http\HttpServer;
use React\Http\Message\Response;

use ModelContextProtocol\Server\SSEServerTransport;
use ModelContextProtocol\Server\Server;
use ModelContextProtocol\Server\ServerOptions;

require 'vendor/autoload.php';

function main(array $args): void {
    $command = $args[1] ?? '--sse-server-ktor';
    $port = $args[2] ?? 3001;

    switch ($command) {
        case '--stdio':
            runMcpServerUsingStdio();
            break;
        case '--sse-server':
            runSseMcpServerWithPlainConfiguration((int)$port);
            break;
        default:
            fwrite(STDERR, "Unknown command: $command" . PHP_EOL);
    }
}

function configureServer(): Server {
    $server = new Server(
        new Implementation('mcp-php test server', '0.1.0'),
        new ServerOptions(
            new ServerCapabilities(
                prompts: new PromptsCapability(listChanged: true),
                resources: new ResourcesCapability(subscribe: true, listChanged: true),
                tools: new ToolsCapability(listChanged: true)
            )
        )
    );

    // Add a prompt
    $server->addPrompt('Kotlin Developer', 'Develop small kotlin applications', [
        new PromptArgument('Project Name', 'Project name for the new project', true)
    ], function ($request) {
        return new GetPromptResult(
            "Description for {$request->name}",
            [
                new PromptMessage(
                    role: Role::USER,
                    content: new TextContent(
                        "Develop a kotlin project named <name>{$request->arguments['Project Name']}</name>"
                    )
                )
            ]
        );
    });

    // Add a tool
    $server->addTool('Test Tool', 'A test tool', new ToolInput(), function ($request) {
        return new CallToolResult([new TextContent('Hello, world!')]);
    });

    // Add a resource
    $server->addResource(
        'https://search.com/',
        'Web Search',
        'Web search engine',
        'text/html',
        function ($request) {
            return new ReadResourceResult([
                new TextResourceContents(
                    "Placeholder content for {$request->uri}",
                    $request->uri,
                    'text/html'
                )
            ]);
        }
    );

    return $server;
}

function runMcpServerUsingStdio(): void {
    $server = configureServer();
    $transport = new StdioServerTransport();

    echo "Server running on stdio" . PHP_EOL;
    $server->connect($transport);

    $done = false;
    $server->onCloseCallback = function () use (&$done) {
        $done = true;
    };

    while (!$done) {
        usleep(10000); // Simulating an async loop
    }

    echo "Server closed" . PHP_EOL;
}

function runSseMcpServerWithPlainConfiguration(int $port): void {
    $loop = Factory::create();
    $servers = [];

    $httpServer = new HttpServer(function ($request) use (&$servers) {
        $path = $request->getUri()->getPath();
        
        var_dump($path);

        if ($path === '/sse') {
            $sessionId = uniqid();
            $transport = new SSEServerTransport('/message', $sessionId);
            $server = configureServer();

            $servers[$sessionId] = $server;
            $server->onCloseCallback = function () use (&$servers, $sessionId) {
                unset($servers[$sessionId]);
            };

            $server->connect($transport);
            return new Response(200, [], "SSE connection established");
        }

        if ($path === '/message') {
            $queryParams = $request->getQueryParams();
            $sessionId = $queryParams['sessionId'] ?? null;

            if (!isset($servers[$sessionId])) {
                return new Response(404, [], "Session not found");
            }

            $transport = $servers[$sessionId]->transport;
            $body = (string)$request->getBody();

            try {
                $transport->handleMessage($body);
                return new Response(202, [], "Accepted");
            } catch (Throwable $e) {
                return new Response(400, [], "Error handling message: " . $e->getMessage());
            }
        }

        return new Response(404, [], "Not Found");
    });

    $socket = new SocketServer("0.0.0.0:$port", $loop);
    $httpServer->listen($socket);

    echo "Starting SSE server on port $port" . PHP_EOL;
    $loop->run();
}

// Start the program
main($argv);
