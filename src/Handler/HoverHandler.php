<?php

namespace Raideer\MagentoIntellisense\Handler;

use Amp\DeferredCancellation;
use Phpactor\LanguageServerProtocol\HoverParams;
use Phpactor\LanguageServerProtocol\ServerCapabilities;
use Raideer\MagentoIntellisense\Handler\Api\CanRegisterCapabilities;
use Raideer\MagentoIntellisense\Server\Api\MessageHandlerInterface;
use Raideer\MagentoIntellisense\Server\Workspace;

final class HoverHandler implements MessageHandlerInterface, CanRegisterCapabilities
{
    public function __construct(
        private Workspace $workspace
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function methods(): array
    {
        return [
            'textDocument/hover' => 'hover',
        ];
    }

    public function hover(array $params, DeferredCancellation $cancellation)
    {
        $hoverParams = HoverParams::fromArray($params, true);
        $document = $this->workspace->get($hoverParams->textDocument->uri);

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function registerCapabilities(ServerCapabilities $capabilities): void
    {
        $capabilities->hoverProvider = true;
    }
}
