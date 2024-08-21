<?php

namespace Raideer\MagentoIntellisense\Handler;

use DI\Container;
use Phpactor\LanguageServerProtocol\InitializeParams;
use Phpactor\LanguageServerProtocol\InitializeResult;
use Phpactor\LanguageServerProtocol\ServerCapabilities;
use Psr\Log\LoggerInterface;
use Raideer\MagentoIntellisense\Handler\Api\CanRegisterCapabilities;
use Raideer\MagentoIntellisense\Server\Api\MessageHandlerInterface;
use Raideer\MagentoIntellisense\Server\HandlerPool;

final class InitializeHandler implements MessageHandlerInterface
{
    public function __construct(
        private Container $container,
        private LoggerInterface $logger
    ) {
    }
    
    public function methods(): array
    {
        return [
            'initialize' => 'handle',
        ];
    }

    public function handle($params): mixed
    {
        $serverCapabilities = new ServerCapabilities();

        $initializeParams = InitializeParams::fromArray($params, true);
        $this->container->set(InitializeParams::class, $initializeParams);
        
        $pool = $this->container->get(HandlerPool::class);

        foreach ($pool->handlers() as $handler) {
            if ($handler instanceof CanRegisterCapabilities) {
                $handler->registerCapabilities($serverCapabilities);
            }
        }

        $result = new InitializeResult(
            $serverCapabilities,
            [
                'name' => 'Magento Intellisense',
                'version' => '1.0.0'
            ],
        );

        $this->logger->info('Responding with initialize params');

        return $result;
    }
}
