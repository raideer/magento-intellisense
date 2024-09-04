<?php

namespace Raideer\MagentoIntellisense\Indexer;

use Phpactor\LanguageServerProtocol\InitializeParams;
use Raideer\MagentoIntellisense\Indexer\Api\IndexInterface;

final class IndexStorage
{
    private string $storageDir;

    public function __construct(private InitializeParams $params)
    {
        if (strtoupper(substr(php_uname('s'), 0, 3)) === 'WIN') {
            $this->storageDir = getenv('LOCALAPPDATA') . '\\MagentoIntellisense\\';
        } elseif (getenv('XDG_CACHE_HOME')) {
            $this->storageDir = getenv('XDG_CACHE_HOME') . '/magento-intellisense/';
        } else {
            $this->storageDir = getenv('HOME') . '/.magento-intellisense/';
        }
    }

    /**
     * @param IndexInterface $index 
     * @return void 
     */
    public function save(IndexInterface $index)
    {
        $data = $index->serialize();

        if (!$data) {
            return;
        }

        $key = $this->getIndexKey($index);
        $file = $this->storageDir . $key;

        if (!file_exists($this->storageDir)) {
            mkdir($this->storageDir);
        }

        file_put_contents($file, gzcompress($data));
    }

    /**
     * @param IndexInterface $index 
     * @return bool 
     */
    public function load(IndexInterface $index): bool
    {
        $key = $this->getIndexKey($index);
        $file = $this->storageDir . $key;

        if (!file_exists($file)) {
            return false;
        }

        $data = file_get_contents($file);

        if ($data === false) {
            return false;
        }

        $index->unserialize(gzuncompress($data));

        return true;
    }

    /**
     * @param IndexInterface $index 
     * @return string 
     */
    private function getIndexKey(IndexInterface $index): string
    {
        return md5($this->params->rootPath . '-' . get_class($index));
    }
}
