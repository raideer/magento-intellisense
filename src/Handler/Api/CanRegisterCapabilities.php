<?php

namespace Raideer\MagentoIntellisense\Handler\Api;

use Phpactor\LanguageServerProtocol\ServerCapabilities;

interface CanRegisterCapabilities
{
    public function registerCapabilities(ServerCapabilities $capabilities): void;
}
