<?php

namespace Raideer\MagentoIntellisense\Indexer;

use Psr\Log\LoggerInterface;
use Raideer\MagentoIntellisense\Server\WorkDone\Client;
use Raideer\MagentoIntellisense\Server\WorkDone\Token;
use Symfony\Component\Stopwatch\Stopwatch;

final class Indexer
{
    public function __construct(
        private Client $workDoneClient,
        private IndexerPool $indexerPool,
        private LoggerInterface $logger,
        private Stopwatch $stopwatch
    ) {
    }

    /**
     * @param string $path 
     * @return void 
     */
    public function index(string $path): void
    {
        $workDoneToken = Token::generate();

        $this->workDoneClient->create($workDoneToken);
        $this->workDoneClient->begin($workDoneToken, 'Magento Intellisense', 'Indexing');

        $indexers = $this->indexerPool->indexers();

        foreach ($indexers as $indexer) {
            $sw = $this->stopwatch->start($indexer->name());

            if (!$indexer->restore()) {
                $indexer->index($path, $workDoneToken);
                $indexer->save();
            } else {
                $this->logger->info('Loaded "' . $indexer->name() . '" index from storage');
            }

            $this->logger->info('Indexed "' . $indexer->name() . '" in ' . $sw->stop()->getDuration() . 'ms');
        }

        $this->workDoneClient->end($workDoneToken);
    }
}
