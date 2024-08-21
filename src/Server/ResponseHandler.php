<?php

namespace Raideer\MagentoIntellisense\Server;

use Amp\DeferredFuture;
use Raideer\MagentoIntellisense\Server\Rpc\ResponseMessage;

final class ResponseHandler
{
    /**
     * @var array<string, DeferredFuture>
     */
    private array $watchers = [];

    /**
     * Handle a response message
     * 
     * @param ResponseMessage $response 
     * @return void 
     */
    public function handle(ResponseMessage $response): void
    {
        if (!isset($this->watchers[$response->id])) {
            return;
        }

        $this->watchers[$response->id]->complete($response);
        unset($this->watchers[$response->id]);
    }

    /**
     * Wait for a response with the given id
     * 
     * @param string $id 
     * @return DeferredFuture 
     */
    public function wait(string $id): DeferredFuture
    {
        $future = new DeferredFuture();
        $this->watchers[$id] = $future;
        return $future;
    }
}
