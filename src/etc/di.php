<?php

use Amp\ByteStream\ReadableResourceStream;
use Amp\ByteStream\WritableResourceStream;
use DI\Container;
use Raideer\MagentoIntellisense\Handler\InitializeHandler;
use Raideer\MagentoIntellisense\Server\Api\TransportInterface;
use Raideer\MagentoIntellisense\Server\HandlerPool;
use Raideer\MagentoIntellisense\Server\Transport\Stdio;

return [
    TransportInterface::class => function () {
        return new Stdio(
            new ReadableResourceStream(STDIN),
            new WritableResourceStream(STDOUT)
        );
    },
    HandlerPool::class => function (Container $c) {
        return new HandlerPool(
            $c->get(InitializeHandler::class)
        );
    },
];
