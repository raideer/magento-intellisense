<?php

namespace Raideer\MagentoIntellisense\Indexer\Api;

interface IndexInterface
{
    public function serialize(): string;
    public function unserialize(string $data): void;
}
