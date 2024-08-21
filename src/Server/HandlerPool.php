<?php

namespace Raideer\MagentoIntellisense\Server;

use Raideer\MagentoIntellisense\Server\Api\MessageHandlerInterface;

final class HandlerPool
{
    /**
     * @var array<string, MessageHandlerInterface>
     */
    private array $handlers;
    
    public function __construct(
        MessageHandlerInterface ...$handlers
    ) {
        $this->handlers = [];

        foreach ($handlers as $handler) {
            foreach ($handler->methods() as $method => $handlerMethod) {
                $this->handlers[$method] = $handler;
            }
        }
    }

    /**
     * @return array<string, MessageHandlerInterface>
     */
    public function handlers(): array
    {
        return $this->handlers;
    }

    /**
     * @param string $handler 
     * @return null|MessageHandlerInterface 
     */
    public function get(string $handler): ?MessageHandlerInterface
    {
        return $this->handlers[$handler] ?? null;
    }
}