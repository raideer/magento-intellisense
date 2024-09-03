<?php

namespace Raideer\MagentoIntellisense\Indexer\Api;

use SplFileInfo;

interface ParserInterface
{
    public function parse(SplFileInfo $file, string $contents): mixed;
    public function clear(): void;
}
