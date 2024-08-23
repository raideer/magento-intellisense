<?php

namespace Raideer\MagentoIntellisense\Handler;

use Phpactor\LanguageServerProtocol\DidChangeTextDocumentParams;
use Phpactor\LanguageServerProtocol\DidCloseTextDocumentParams;
use Phpactor\LanguageServerProtocol\DidOpenTextDocumentParams;
use Phpactor\LanguageServerProtocol\DidSaveTextDocumentParams;
use Phpactor\LanguageServerProtocol\ServerCapabilities;
use Phpactor\LanguageServerProtocol\TextDocumentSyncKind;
use Raideer\MagentoIntellisense\Event\TextDocumentChanged;
use Raideer\MagentoIntellisense\Event\TextDocumentClosed;
use Raideer\MagentoIntellisense\Event\TextDocumentOpened;
use Raideer\MagentoIntellisense\Event\TextDocumentSaved;
use Raideer\MagentoIntellisense\Handler\Api\CanRegisterCapabilities;
use Raideer\MagentoIntellisense\Server\Api\MessageHandlerInterface;
use Raideer\MagentoIntellisense\Server\Workspace;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TextDocumentHandler implements MessageHandlerInterface, CanRegisterCapabilities
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private Workspace $workspace,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function methods(): array
    {
        return [
            'textDocument/didOpen' => 'didOpen',
            'textDocument/didChange' => 'didChange',
            'textDocument/didClose' => 'didClose',
            'textDocument/didSave' => 'didSave',
        ];
    }

    /**
     * @param DidOpenTextDocumentParams $params 
     * @return void 
     */
    public function didOpen(DidOpenTextDocumentParams $params): void
    {
        $this->workspace->open($params->textDocument);
        $this->eventDispatcher->dispatch(new TextDocumentOpened($params->textDocument));
    }

    /**
     * @param DidChangeTextDocumentParams $params 
     * @return void 
     */
    public function didChange(DidChangeTextDocumentParams $params): void
    {
        foreach ($params->contentChanges as $change) {
            $this->workspace->update($params->textDocument, $change['text']);
            $this->eventDispatcher->dispatch(new TextDocumentChanged($params->textDocument, $change['text']));
        }
    }

    /**
     * @param DidCloseTextDocumentParams $params 
     * @return void 
     */
    public function didClose(DidCloseTextDocumentParams $params): void
    {
        $this->workspace->close($params->textDocument);
        $this->eventDispatcher->dispatch(new TextDocumentClosed($params->textDocument));
    }

    /**
     * @param DidSaveTextDocumentParams $params 
     * @return void 
     */
    public function didSave(DidSaveTextDocumentParams $params): void
    {
        $this->eventDispatcher->dispatch(new TextDocumentSaved($params->textDocument, $params->text));
    }

    /**
     * {@inheritdoc}
     */
    public function registerCapabilities(ServerCapabilities $capabilities): void
    {
        $capabilities->textDocumentSync = TextDocumentSyncKind::FULL;
    }
}
