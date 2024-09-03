<?php

namespace Raideer\MagentoIntellisense\Indexer\Api;

use Raideer\MagentoIntellisense\Server\WorkDone\Token;

interface IndexerInterface
{
    /**
     * @param string $path 
     * @param Token $token
     * 
     * @return void 
     */
    public function index(string $path, Token $token): void;
}
