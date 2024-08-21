<?php

namespace Raideer\MagentoIntellisense\Server\Rpc;

use Raideer\MagentoIntellisense\Server\Exception\InvalidMessageException;

final class MessageFactory
{
    /**
     * Takes in a RawMessage and returns the appropriate Message object
     * 
     * @param RawMessage $message 
     * @return null|Message 
     * @throws InvalidMessageException 
     */
    public function create(RawMessage $message): ?Message
    {
        $body = $message->body;

        unset($body['jsonrpc']);

        if (!isset($body['id']) && isset($body['method'])) {
            return new NotificationMessage($body['method'], $body['params']);
        }

        if (isset($body['id']) && isset($body['method'])) {
            return new RequestMessage($body['id'], $body['method'], $body['params']);
        }

        if (isset($body['result']) || isset($body['error'])) {
            return new ResponseMessage($body['id'], $body['result'], $body['error']);
        }

        throw new InvalidMessageException('Invalid message');
    }
}