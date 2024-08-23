<?php

namespace Raideer\MagentoIntellisense\Handler;

use DI\Container;
use Phpactor\LanguageServerProtocol\InitializeParams;
use Psr\Log\LoggerInterface;
use Raideer\MagentoIntellisense\Server\Api\MessageHandlerInterface;
use Raideer\MagentoIntellisense\Event\Initialized;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class InitializedHandler implements MessageHandlerInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private Container $container,
        private LoggerInterface $logger
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
        
        $this->eventDispatcher->dispatch(
            new Initialized(
                $this->container->get(InitializeParams::class),
            ),
        );
    }
}
