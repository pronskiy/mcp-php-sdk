<?php

namespace ModelContextProtocol\Server;

use ModelContextProtocol\Shared\ReadBuffer;
use ModelContextProtocol\Shared\Transport;
use ModelContextProtocol\Types\JSONRPCMessage;
use React\EventLoop\Loop;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableResourceStream;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableStreamInterface;

class StdioServerTransport implements Transport
{
    private \React\EventLoop\LoopInterface $loop;
    private bool $started = false;

    private ReadableStreamInterface $stdin;
    private WritableStreamInterface $stdout;

    public ?\Closure $onMessage = null;
    public ?\Closure $onClose = null;
    public ?\Closure $onData;
    public ?\Closure $onError;


    private ReadBuffer $readBuffer;

    public function __construct(
        ?ReadableStreamInterface $stdin = null,
        ?WritableStreamInterface $stdout = null,
    )
    {
        $this->stdin = $stdin ?? new ReadableResourceStream(STDIN, Loop::get());
        $this->stdout = $stdout ?? new WritableResourceStream(STDOUT, Loop::get());
//        $this->loop = $loop ?? new Loop();

        $this->readBuffer = new ReadBuffer();
    }

    public function start(): void
    {
        if ($this->started) {
            throw new \Exception("Transport already started");
        }

        $this->started = true;
//        $this->loop->addReadStream(STDIN, function ($stream) {
//            $line = fgets($stream);
//            // Handle incoming line
//        });

        $this->onData = function (string $chunk): void {
            $this->readBuffer->append($chunk);
            $this->processReadBuffer();
        };

        $this->stdin->on('data', $this->onData);

        $this->onError = function (Exception $e): void {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
            if ($this->onError !== null) {
                ($this->onError)($exception);
            }
        };
        $this->stdin->on('error', $this->onError);
    }

    private function processReadBuffer()
    {
        while (true) {
            try {
                $message = $this->readBuffer->readMessage();
                if ($message === null) {
                    break;
                }

                if ($this->onMessage !== null) {
                    ($this->onMessage)($message);
                }
            } catch (Exception $exception) {
                if ($this->onError !== null) {
                    ($this->onError)($exception);
                }
            }
        }
    }

    public function close(): void
    {
        // Remove our event listeners first
        $this->stdin->removeListener('data', $this->onData);
        $this->stdin->removeListener('error', $this->onError);

        // Check if we were the only data listener
        $remainingDataListeners = count($this->stdin->listeners('data'));
        if (remainingDataListeners === 0) {
            // Only pause stdin if we were the only listener
            // This prevents interfering with other parts of the application that might be using stdin
            $this->stdin->pause();
        }

        // Clear the buffer and notify closure
        $this->readBuffer->clear();
        if ($this->onClose !== null) {
            $this->onClose();
        }
    }

    public function send(JSONRPCMessage $message)
    {
//        return new Promise((resolve) => {
        $json = ReadBuffer::serializeMessage($message);
        if ($this->stdout->write($json)) {
//            resolve();
        } else {
//            $this->stdout->once("drain", resolve);
//            $this->stdout->once("drain", resolve);
        }
//    });
    }

    public function setOnMessage(callable $onMessage): void
    {
        $this->onMessage = $onMessage;
    }

    public function setOnClose(callable $onClose): void
    {
        $this->onClose = $onClose;
    }

    public function setOnError(callable $onError): void
    {
        $this->onError = $onError;
    }
}
