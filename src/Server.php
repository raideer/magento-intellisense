<?php

namespace Raideer\MagentoIntellisense;

use Psr\Log\LoggerInterface;
use Raideer\MagentoIntellisense\Server\Api\TransportInterface;
use Raideer\MagentoIntellisense\Server\MessageDispatcher;
use Raideer\MagentoIntellisense\Server\Rpc\Client;
use Raideer\MagentoIntellisense\Server\Rpc\Message;
use Raideer\MagentoIntellisense\Server\Rpc\MessageFactory;
use Raideer\MagentoIntellisense\Server\Rpc\MessageReader;

use function Amp\async;

final class Server
{
    public function __construct(
        private LoggerInterface $logger,
        private TransportInterface $transport,
        private MessageReader $reader,
        private MessageFactory $messageFactory,
        private MessageDispatcher $dispatcher,
        private Client $rpcClient
    ) {
    }
   
    /**
     * Runs the Language Server
     *
     * @return void
     */
    public function run()
    {
        $this->logger->info('Starting Magento Intellisense');

        while (null !== $chunk = $this->transport->read()) {
            $this->processChunk($chunk);
        }

        $this->logger->info('Server stopped');
    }

    /**
     * Processes the incoming chunk of data
     *
     * @param string $chunk
     * @return void
     */
    private function processChunk(string $chunk)
    {
        $rawMessage = $this->reader->read($chunk);

        if ($rawMessage === null) {
            return;
        }

        $message = $this->messageFactory->create($rawMessage);

        async(function () use ($message) {
            $this->dispatch($message);
        });
    }

    /**
     * @param Message $message
     * @return void
     */
    private function dispatch(Message $message)
    {
        $response = $this->dispatcher->dispatch($message);

        if ($response === null) {
            return;
        }

        $this->rpcClient->respond($response);
    }
}
