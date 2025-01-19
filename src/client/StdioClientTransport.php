<?php

namespace ModelContextProtocol\Client;

use ModelContextProtocol\Types\JSONRPCRequest;
use ModelContextProtocol\Types\JSONRPCResponse;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;
use Symfony\Component\Process\Process;

class StdioClientTransport implements Transport {
    private $process;
    private $options;
    private $stdin;
    private $stdout;
    private $loop;

    public function __construct(array $options, ?\React\EventLoop\LoopInterface $loop = null) {
        $this->options = $options;
        $this->loop = $loop ?? \React\EventLoop\Factory::create();
    }

    public function connect(): void {
        $command = $this->options['command'];
        $args = $this->options['args'] ?? [];

        $this->process = new Process(array_merge([$command], $args));
        $this->process->start();

        $this->stdin = $this->process->getInput();
        $this->stdout = $this->process->getOutput();
    }

    public function sendRequest(JSONRPCRequest $request): \React\Promise\PromiseInterface {
        return new \React\Promise\Promise(function ($resolve, $reject) use ($request) {
            try {
                $jsonRequest = json_encode($request) . "\n";
                $this->stdin->write($jsonRequest);

                // Read response
                $response = '';
                while (!$this->process->isTerminated()) {
                    $output = $this->stdout->read();
                    if ($output === null) {
                        continue;
                    }
                    $response .= $output;
                    if (str_ends_with(trim($response), '}')) {
                        $resolve(JSONRPCResponse::fromJson($response));
                        break;
                    }
                }
            } catch (\Exception $e) {
                $reject($e);
            }
        });
    }

    public function close(): void {
        if ($this->process && $this->process->isRunning()) {
            $this->process->stop();
        }
    }
}
