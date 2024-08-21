<?php

namespace Raideer\MagentoIntellisense\Server\Api;

interface TransportInterface
{
    /**
     * Reads data from the stream
     * 
     * @return null|string 
     */
    public function read(): ?string;

    /**
     * Writes data to the stream
     * 
     * @param string $data 
     * @return void 
     */
    public function write(string $data): void;

    /**
     * Ends the stream
     * 
     * @return void 
     */
    public function end(): void;

    /**
     * Forcefully closes the stream
     * 
     * @return void 
     */
    public function close(): void;
}
