<?php

namespace Raideer\MagentoIntellisense\Indexer;

use SplFileInfo;

class ContentLoader
{
    private array $cache;

    /**
     * @param SplFileInfo $file 
     * @return null|string 
     */
    public function load(SplFileInfo $file): ?string
    {
        if (isset($this->cache[$file->getPathname()])) {
            return $this->cache[$file->getPathname()];
        }

        $contents = @file_get_contents($file->getPathname());

        if ($contents === false) {
            return null;
        }

        $this->cache[$file->getPathname()] = $contents;

        return $contents;
    }

    /**
     * @return void 
     */
    public function clear(): void
    {
        $this->cache = [];
    }
}