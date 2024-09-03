<?php

namespace Raideer\MagentoIntellisense\Handler;

use DI\Container;
use Phpactor\LanguageServerProtocol\InitializeParams;
use Psr\Log\LoggerInterface;
use Raideer\MagentoIntellisense\Server\Api\MessageHandlerInterface;
use Raideer\MagentoIntellisense\Event\Initialized;
use Raideer\MagentoIntellisense\Indexer\Indexer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class InitializedHandler implements MessageHandlerInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private Container $container,
        private LoggerInterface $logger,
        private Indexer $indexer
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function methods(): array
    {
        return [
            'initialized' => 'handle',
        ];
    }

    public function handle()
    {
        $this->logger->info('Initialized');

        /** @var InitializeParams $params */
        $params = $this->container->get(InitializeParams::class);

        if ($params->rootUri) {
            $this->indexer->index(
                $params->rootUri
            );
        }

        $this->eventDispatcher->dispatch(
            new Initialized(
                $params,
            ),
        );
    }
}
