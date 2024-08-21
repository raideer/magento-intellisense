<?php

namespace Raideer\MagentoIntellisense\Event;

use Phpactor\LanguageServerProtocol\InitializeParams;

class Initialized
{
    public function __construct(
        public InitializeParams $params,
    ) {
    }
}
