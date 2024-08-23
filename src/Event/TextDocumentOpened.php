<?php

namespace Raideer\MagentoIntellisense\Event;

use Phpactor\LanguageServerProtocol\TextDocumentItem;

class TextDocumentOpened
{
    public function __construct(
        public TextDocumentItem $params,
    ) {
    }
}
