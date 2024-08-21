<?php

namespace Raideer\MagentoIntellisense\Server\Rpc;

use Generator;
use Raideer\MagentoIntellisense\Server\Exception\InvalidMessageException;

final class MessageReader
{
    const STATE_HEADERS = 1;
    const STATE_BODY = 2;

    private string $buffer = '';
    private array $headers = [];
    private int $contentLength = 0;
    private int $state = self::STATE_HEADERS;

    /**
     * @param string $input 
     * @return RawMessage 
     * @throws InvalidMessageException 
     */
    public function read(string $input): ?RawMessage
    {
        foreach (str_split($input) as $char) {
            $this->buffer .= $char;

            if ($this->state === self::STATE_HEADERS) {
                if ($this->buffer === "\r\n") {
                    if (!isset($this->headers['Content-Length'])) {
                        throw new InvalidMessageException('Invalid message: missing Content-Length header');
                    }

                    $this->state = self::STATE_BODY;
                    $this->buffer = '';
                    $this->contentLength = (int) $this->headers['Content-Length'];
                } else if (substr($this->buffer, -2) === "\r\n") {
                    [$header, $value] = explode(':', $this->buffer);
                    $this->headers[$header] = trim($value);
                    $this->buffer = '';
                }

                continue;
            }

            if ($this->state === self::STATE_BODY) {
                if (strlen($this->buffer) === $this->contentLength) {
                    $message = new RawMessage($this->headers, $this->parseBody());

                    $this->state = self::STATE_HEADERS;
                    $this->buffer = '';
                    $this->headers = [];

                    return $message;
                }
            }
        }

        return null;
    }

    /**
     * @return array 
     * @throws InvalidMessageException 
     */
    private function parseBody(): array
    {
        $body = json_decode($this->buffer, true);

        if (!is_array($body)) {
            throw new InvalidMessageException('Invalid message: body is not an array');
        }

        return $body;
    }
}