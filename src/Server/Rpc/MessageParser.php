<?php

namespace Raideer\MagentoIntellisense\Server\Rpc;

use Raideer\MagentoIntellisense\Server\Exception\InvalidMessageException;

final class MessageParser
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
    public function parse(string $input): ?RawMessage
    {
        foreach (str_split($input) as $char) {
            $this->buffer .= $char;

            switch ($this->state) {
                case self::STATE_HEADERS:
                    $this->readHeaders();
                    break;
                case self::STATE_BODY:
                    if ($message = $this->readBody()) {
                        return $message;
                    }
                    break;
            }
        }

        return null;
    }

    /**
     * @return void 
     * @throws InvalidMessageException 
     */
    private function readHeaders(): void
    {
        if ($this->buffer === "\r\n") {
            if (!isset($this->headers['Content-Length'])) {
                throw new InvalidMessageException('Invalid message: missing Content-Length header');
            }

            $this->state = self::STATE_BODY;
            $this->buffer = '';
            $this->contentLength = (int) $this->headers['Content-Length'];
        } elseif (substr($this->buffer, -2) === "\r\n") {
            [$header, $value] = explode(':', $this->buffer);
            $this->headers[$header] = trim($value);
            $this->buffer = '';
        }
    }

    /**
     * @return null|RawMessage
     * @throws InvalidMessageException
     */
    private function readBody(): ?RawMessage
    {
        if (strlen($this->buffer) !== $this->contentLength) {
            return null;
        }
        
        $message = new RawMessage($this->headers, $this->parseBuffer());

        $this->state = self::STATE_HEADERS;
        $this->buffer = '';
        $this->headers = [];

        return $message;
    }

    /**
     * @return array
     * @throws InvalidMessageException
     */
    private function parseBuffer(): array
    {
        $body = json_decode($this->buffer, true);

        if (!is_array($body)) {
            throw new InvalidMessageException('Invalid message: body is not an array');
        }

        return $body;
    }
}
