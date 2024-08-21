<?php

namespace Raideer\MagentoIntellisense\Server\Rpc;

final class RawMessage
{
    public function __construct(
        public array $headers,
        public array $body,
    ) {
    }
}
