<?php

namespace Raideer\MagentoIntellisense\Handler;

use Phpactor\LanguageServerProtocol\DefinitionRequest;
use Phpactor\LanguageServerProtocol\LocationLink;
use Phpactor\LanguageServerProtocol\ServerCapabilities;
use Phpactor\LanguageServerProtocol\TextDocumentItem;
use Phpactor\LanguageServerProtocol\TypeDefinitionParams;
use Raideer\MagentoIntellisense\Config\TypeDefinitions\Definition;
use Raideer\MagentoIntellisense\Handler\Api\CanRegisterCapabilities;
use Raideer\MagentoIntellisense\Server\Api\MessageHandlerInterface;
use Raideer\MagentoIntellisense\Server\Workspace;

class TypeDefinitionHandler implements MessageHandlerInterface, CanRegisterCapabilities
{
    public function __construct(
        private Workspace $workspace,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function methods(): array
    {
        return [
            // TypeDefinitionRequest::METHOD => 'definition',
            DefinitionRequest::METHOD => 'definition',
        ];
    }

    /**
     * @param TypeDefinitionParams $params
     * @return null|LocationLink
     */
    public function definition(TypeDefinitionParams $params)
    {
        $document = $this->workspace->get($params->textDocument->uri);
        return null;
    }

    /**
     * @param ServerCapabilities $capabilities
     * @return void
     */
    public function registerCapabilities(ServerCapabilities $capabilities): void
    {
        $capabilities->typeDefinitionProvider = true;
        $capabilities->definitionProvider = true;
    }

    // /**
    //  * @param DefinitionData $data
    //  * @return LocationLink
    //  */
    // public static function definitionDataToLocationLink(
    //     DefinitionData $data
    // ): LocationLink {
    //     if (!($data->record instanceof HasPosition) || !($data->record instanceof HasUri)) {
    //         throw new RuntimeException('Trying to resolve an invalid record');
    //     }

    //     /** @var Token */
    //     $token = $data->additionalData['token'];

    //     $startPos = $data->record->startPosition();
    //     $startPos->character = 0;

    //     $targetRange = new Range($startPos, $data->record->endPosition());

    //     $originSelectionStart = PositionConverter::offsetToPosition($token->fullOffset + 1, $data->document->text);
    //     $originSelectionEnd = PositionConverter::offsetToPosition($token->fullOffset + strlen($token->fullValue) + 1, $data->document->text);

    //     $originSelectionRange = new Range($originSelectionStart, $originSelectionEnd);

    //     return new LocationLink(
    //         $data->record->uri(),
    //         $targetRange,
    //         $targetRange,
    //         $originSelectionRange,
    //     );
    // }
}
