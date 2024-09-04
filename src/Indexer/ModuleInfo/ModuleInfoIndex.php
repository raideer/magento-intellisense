<?php

namespace Raideer\MagentoIntellisense\Indexer\ModuleInfo;

use Raideer\MagentoIntellisense\Indexer\Api\IndexInterface;
use Raideer\MagentoIntellisense\Indexer\ModuleInfo\Data\Module;

final class ModuleInfoIndex implements IndexInterface
{
    /**
     * @var Module[]
     */
    private array $modules = [];

    /**
     * @param Module $module 
     * @return void 
     */
    public function addModule(Module $module): void
    {
        $this->modules[$module->name] = $module;
    }

    /**
     * @return Module[] 
     */
    public function modules()
    {
        return $this->modules;
    }

    /**
     * @param string $name 
     * @return null|Module 
     */
    public function getModule(string $name): ?Module
    {
        return $this->modules[$name] ?? null;
    }

    /**
     * @return string 
     */
    public function serialize(): string
    {
        return serialize($this->modules);
    }

    /**
     * @param string $data 
     * @return void 
     */
    public function unserialize(string $data): void
    {
        $this->modules = unserialize($data);
    }
}