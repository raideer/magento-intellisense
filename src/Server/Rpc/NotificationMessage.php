<?php

namespace Raideer\MagentoIntellisense\Server\Rpc;

/**
 * Represents a notification message
 */
final class NotificationMessage extends Message
{
    public function __construct(
        public string $method,
        public array $params,
    ) {
    }
}
