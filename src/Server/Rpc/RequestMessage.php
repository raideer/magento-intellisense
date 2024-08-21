<?php

namespace Raideer\MagentoIntellisense\Server\Rpc;

/**
 * Represents a request message that desribes a request
 * between the client and the server
 */
final class RequestMessage extends Message
{
    public function __construct(
        public string|int $id,
        public string $method,
        public ?array $params = [],
    ) {
    }
}
