<?php

namespace Raideer\MagentoIntellisense\Indexer;

use Raideer\MagentoIntellisense\Indexer\Api\IndexerInterface;

class IndexerPool
{
    private array $indexers;

    public function __construct(
        IndexerInterface ...$indexers
    ) {
        $this->indexers = $indexers;
    }

    /**
     * @return IndexerInterface[]
     */
    public function indexers()
    {
        return $this->indexers;
    }
}
