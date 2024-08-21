<?php

namespace Raideer\MagentoIntellisense\Server\Transport;

use Amp\ByteStream\ReadableResourceStream;
use Amp\ByteStream\WritableResourceStream;
use Raideer\MagentoIntellisense\Server\Api\TransportInterface;

class Stdio implements TransportInterface
{
    public function __construct(
        private ReadableResourceStream $inputStream,
        private WritableResourceStream $outputStream,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function read(): ?string
    {
        return $this->inputStream->read();
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $data): void
    {
        $this->outputStream->write($data);
    }

    /**
     * {@inheritdoc}
     */
    public function end(): void
    {
        $this->outputStream->end();
    }

    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
        $this->inputStream->close();
    }
}
