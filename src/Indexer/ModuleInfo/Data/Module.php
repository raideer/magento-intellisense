<?php

namespace Raideer\MagentoIntellisense\Indexer\ModuleInfo\Data;

class Module
{
    public string $name;
    public string $uri;
    public array $sequence;
    public ?string $version;
    public string $directory;
}