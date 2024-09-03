<?php

namespace Raideer\MagentoIntellisense\Indexer;

use Raideer\MagentoIntellisense\Server\WorkDone\Client;
use Raideer\MagentoIntellisense\Server\WorkDone\Token;

final class Indexer
{
    public function __construct(
        private Client $workDoneClient,
        private IndexerPool $indexerPool
    ) {
    }

    public function index(string $path): void
    {
        $workDoneToken = Token::generate();

        $this->workDoneClient->create($workDoneToken);
        $this->workDoneClient->begin($workDoneToken, 'Magento Intellisense', 'Indexing');

        $indexers = $this->indexerPool->indexers();

        foreach ($indexers as $indexer) {
            $indexer->index($path, $workDoneToken);
        }

        $this->workDoneClient->end($workDoneToken);
    }
}
