<?php

namespace Raideer\MagentoIntellisense\Indexer\ModuleInfo;

use Raideer\MagentoIntellisense\Indexer\ModuleInfo\Data\Module;

final class ModuleInfoIndex
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
}