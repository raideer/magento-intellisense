<?php

namespace Raideer\MagentoIntellisense\Server\Rpc;

/**
 * Represends a general message in the JSON-RPC protocol
 */
abstract class Message
{
    public string $jsonrpc = '2.0';
}
