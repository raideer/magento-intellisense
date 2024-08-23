<?php

namespace Raideer\MagentoIntellisense\Server;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Phpactor\LanguageServerProtocol\TextDocumentIdentifier;
use Phpactor\LanguageServerProtocol\TextDocumentItem;
use Phpactor\LanguageServerProtocol\VersionedTextDocumentIdentifier;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Workspace implements Countable, IteratorAggregate
{
    /**
     * @var TextDocumentItem[]
     */
    private $documents = [];

    /**
     * @var array<string,int>
     */
    private $documentVersions = [];

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @param string $uri 
     * @return bool 
     */
    public function has(string $uri): bool
    {
        return isset($this->documents[$uri]);
    }

    /**
     * @param string $uri 
     * @return null|TextDocumentItem 
     */
    public function get(string $uri): ?TextDocumentItem
    {
        if (!isset($this->documents[$uri])) {
            return null;
        }

        return $this->documents[$uri];
    }

    /**
     * @param TextDocumentItem $textDocument 
     * @return void 
     */
    public function open(TextDocumentItem $textDocument): void
    {
        $this->documents[$textDocument->uri] = $textDocument;
        $this->documentVersions[$textDocument->uri] = $textDocument->version;
    }

    /**
     * @param VersionedTextDocumentIdentifier $textDocument 
     * @param string $updatedText 
     * @return void 
     */
    public function update(VersionedTextDocumentIdentifier $textDocument, string $updatedText): void
    {
        if (!isset($this->documents[$textDocument->uri])) {
            return;
        }

        // if null, we take assume the new version as authoritative - otherwise
        // if the new version is lower we discard it.
        //
        // the behavior described here:
        // https://microsoft.github.io/language-server-protocol/specification#versionedTextDocumentIdentifier
        // indicates that only the server should send NULL, but specifies no
        // behavior the an update from the client to the server.
        $currentVersion = $this->documentVersions[$textDocument->uri];
        if (null !== $textDocument->version && $currentVersion > $textDocument->version) {
            $this->logger->info(sprintf(
                'Version "%s" of incoming document (%s) is older than current version (%s), ignoring',
                $textDocument->version,
                $textDocument->uri,
                $currentVersion,
            ));
            return;
        }

        $this->documents[$textDocument->uri]->text = $updatedText;
        $this->documents[$textDocument->uri]->version = $textDocument->version;
        $this->documentVersions[$textDocument->uri] = $textDocument->version;
    }

    /**
     * @return int 
     */
    public function openFiles(): int
    {
        return count($this->documents);
    }

    /**
     * @param TextDocumentIdentifier $identifier 
     * @return void 
     */
    public function close(TextDocumentIdentifier $identifier): void
    {
        if (!isset($this->documents[$identifier->uri])) {
            return;
        }

        unset($this->documents[$identifier->uri]);
        unset($this->documentVersions[$identifier->uri]);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return $this->openFiles();
    }

    /**
     * @return ArrayIterator<string,TextDocumentItem>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->documents);
    }
}
