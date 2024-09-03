<?php

namespace Raideer\MagentoIntellisense\Indexer\Parser;

use Raideer\MagentoIntellisense\Indexer\Api\ParserInterface;
use SplFileInfo;

class SimpleXmlParser implements ParserInterface
{
    private array $cache = [];

    /**
     * @inheritdoc
     */
    public function parse(SplFileInfo $file, string $contents): mixed
    {
        if (isset($this->cache[$file->getPathname()])) {
            return $this->cache[$file->getPathname()];
        }
    
        $parsed = simplexml_load_string($contents);

        $this->cache[$file->getPathname()] = $parsed;

        return $parsed;
    }

    /**
     * @inheritdoc
     */
    public function clear(): void
    {
        $this->cache = [];
    }
}
