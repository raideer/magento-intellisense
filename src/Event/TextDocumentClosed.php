<?php

namespace Raideer\MagentoIntellisense\Event;

use Phpactor\LanguageServerProtocol\TextDocumentIdentifier;

class TextDocumentClosed
{
    public function __construct(
        public TextDocumentIdentifier $identifier,
    ) {
    }
}
