<?php

namespace Raideer\MagentoIntellisense\Event;

use Phpactor\LanguageServerProtocol\VersionedTextDocumentIdentifier;

class TextDocumentChanged
{
    public function __construct(
        public VersionedTextDocumentIdentifier $identifier,
        public string $text,
    ) {
    }
}
