<?php

namespace Raideer\MagentoIntellisense\Event;

use Phpactor\LanguageServerProtocol\TextDocumentIdentifier;

class TextDocumentSaved
{
    public function __construct(
        public TextDocumentIdentifier $identifier,
        public ?string $text,
    ) {
    }
}
