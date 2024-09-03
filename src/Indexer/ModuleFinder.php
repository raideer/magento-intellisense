<?php

namespace Raideer\MagentoIntellisense\Indexer;

use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

final class ModuleFinder
{
    private array $moduleCache = [];
    private array $fileCache = [];

    /**
     * @param string $path
     * @param bool $skipCache
     * 
     * @return Finder 
     * @throws DirectoryNotFoundException 
     */
    public function find(string $path, bool $skipCache = false): Finder
    {
        if (!$skipCache && isset($this->moduleCache[$path])) {
            return $this->moduleCache[$path];
        }

        $finder = new Finder();
        $finder
            ->files()
            ->ignoreUnreadableDirs()
            ->in($path)
            ->exclude(['node_modules', 'dev', 'tests', 'pub'])
            ->name('registration.php');

        $this->moduleCache[$path] = $finder;

        return $finder;
    }

    /**
     * @param string $directory 
     * @param bool $skipCache
     * 
     * @return Finder 
     * @throws DirectoryNotFoundException 
     */
    public function findFiles(string $directory, bool $skipCache = false): Finder
    {
        if (!$skipCache && isset($this->fileCache[$directory])) {
            return $this->fileCache[$directory];
        }

        $finder = new Finder();
        $finder->files()
            ->in($directory)
            ->exclude('Tests')
            ->exclude('Test')
            ->exclude('setup')
            ->name('*.php')
            ->name('*.xml');

        return $finder;
    }
}