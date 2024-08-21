<?php

namespace Raideer\MagentoIntellisense\Server\Api;

interface MessageHandlerInterface
{
    /**
     * @return array<string,string>
     */
    public function methods(): array;
}