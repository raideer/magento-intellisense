<?php

namespace Raideer\MagentoIntellisense\Server\Rpc;

use JsonSerializable;

/**
 * Represents a response message that is sent as a result
 * of a RequestMessage
 */
final class ResponseMessage extends Message implements JsonSerializable
{
    public function __construct(
        public $id,
        public $result = null,
        public $error = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        $json = [
            'jsonrpc' => $this->jsonrpc,
            'id' => $this->id,
        ];

        if ($this->error) {
            $json['error'] = $this->error;
        } else {
            $json['result'] = $this->result;
        }

        return $json;
    }
}
