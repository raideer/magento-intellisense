<?php

namespace Raideer\MagentoIntellisense\Indexer\ModuleInfo;

use Raideer\MagentoIntellisense\Indexer\Api\IndexerInterface;
use Raideer\MagentoIntellisense\Indexer\ContentLoader;
use Raideer\MagentoIntellisense\Indexer\ModuleFinder;
use Raideer\MagentoIntellisense\Indexer\ModuleInfo\Data\Module;
use Raideer\MagentoIntellisense\Indexer\ModuleInfo\ModuleInfoIndex;
use Raideer\MagentoIntellisense\Indexer\Parser\SimpleXmlParser;
use Raideer\MagentoIntellisense\Server\WorkDone\Client;
use Raideer\MagentoIntellisense\Server\WorkDone\Token;
use SimpleXMLElement;
use SplFileInfo;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

final class ModuleInfoIndexer implements IndexerInterface
{
    public function __construct(
        private ModuleFinder $moduleFinder,
        private Client $workDoneClient,
        private SimpleXmlParser $parser,
        private ContentLoader $contentLoader,
        private ModuleInfoIndex $index
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function index(string $path, Token $token): void
    {
        $modules = $this->moduleFinder->find($path);

        $i = 1;

        foreach ($modules as $module) {
            if (($i + 1) % 100 === 0) {
                $this->workDoneClient->report(
                    $token,
                    sprintf("Indexed %d/%d modules", $i + 1, $modules->count()),
                );
            }

            $this->indexModule($module);
            $i++;
        }

        return;
    }

    /**
     * @param SplFileInfo $modulePath 
     * @return void 
     * @throws DirectoryNotFoundException 
     */
    private function indexModule(SplFileInfo $modulePath): void
    {
        $files = $this->moduleFinder->findFiles($modulePath->getPath());
        $files = iterator_to_array($files);

        foreach ($files as $file) {
            if (!$this->canIndex($file)) {
                continue;
            }

            $this->indexFile($file);
        }
    }

    /**
     * @param SplFileInfo $file 
     * @return void 
     */
    private function indexFile(SplFileInfo $file): void
    {
        $content = $this->contentLoader->load($file);

        if ($content === null) {
            return;
        }

        $xml = $this->parser->parse($file, $content);

        if ($xml === null) {
            return;
        }

        assert($xml instanceof SimpleXMLElement);

        $module = new Module();
        $module->directory = $file->getPath();
        $moduleElement = $xml->module;

        $attributes = $moduleElement->attributes();

        $module->name = (string) $attributes->name;
        $module->version = !isset($attributes->setup_version) ? null : (string) $attributes->setup_version;

        $sequence = [];

        if (isset($moduleElement->sequence)) {
            foreach ($moduleElement->sequence->module as $item) {
                $sequence[] = (string) $item->attributes()->name;
            }
        }

        $module->sequence = $sequence;

        $this->index->addModule($module);
    }

    /**
     * @param SplFileInfo $file 
     * @return bool 
     */
    private function canIndex(SplFileInfo $file): bool
    {
        return $file->getExtension() === 'xml' && $file->getBasename() === 'module.xml';
    }
}
