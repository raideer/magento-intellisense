<?php

use Amp\ByteStream\ReadableResourceStream;
use Amp\ByteStream\WritableResourceStream;
use DI\Container;
use Raideer\MagentoIntellisense\Handler\HoverHandler;
use Raideer\MagentoIntellisense\Handler\InitializedHandler;
use Raideer\MagentoIntellisense\Handler\InitializeHandler;
use Raideer\MagentoIntellisense\Handler\TextDocumentHandler;
use Raideer\MagentoIntellisense\Server\Api\TransportInterface;
use Raideer\MagentoIntellisense\Server\HandlerPool;
use Raideer\MagentoIntellisense\Server\Transport\Stdio;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

return [
    TransportInterface::class => function () {
        return new Stdio(
            new ReadableResourceStream(STDIN),
            new WritableResourceStream(STDOUT)
        );
    },
    HandlerPool::class => function (Container $c) {
        return new HandlerPool(
            $c->get(InitializeHandler::class),
            $c->get(InitializedHandler::class),
            $c->get(TextDocumentHandler::class),
            $c->get(HoverHandler::class)
        );
    },
    EventDispatcherInterface::class => function () {
        return new EventDispatcher();
    },
];
