<?php

namespace Raideer\MagentoIntellisense\Server;

use Amp\DeferredCancellation;
use Amp\Future;
use Error;
use Exception;
use Psr\Log\LoggerInterface;
use Raideer\MagentoIntellisense\Server\Api\MessageHandlerInterface;
use Raideer\MagentoIntellisense\Server\Rpc\Message;
use Raideer\MagentoIntellisense\Server\Rpc\NotificationMessage;
use Raideer\MagentoIntellisense\Server\Rpc\RequestMessage;
use Raideer\MagentoIntellisense\Server\Rpc\ResponseError;
use Raideer\MagentoIntellisense\Server\Rpc\ResponseMessage;
use Raideer\MagentoIntellisense\Server\Rpc\ResponseMessageBuilder;
use RuntimeException;
use Throwable;

use function Amp\async;

class MessageDispatcher
{
    /**
     * @var array<string, DeferredCancellation>
     */
    private array $cancellations = [];

    public function __construct(
        private ResponseHandler $responseHandler,
        private HandlerPool $handlers,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @param Message $message
     * @return null|ResponseMessage
     */
    public function dispatch(Message $message): ?ResponseMessage
    {
        if ($message instanceof ResponseMessage) {
            $this->responseHandler->handle($message);
            return null;
        }

        assert(($message instanceof RequestMessage) || ($message instanceof NotificationMessage));

        if ($this->isCancellationMessage($message)) {
            $this->cancel($message->params['id']);
            return null;
        }

        $handler = $this->handlers->get($message->method);

        if (!$handler) {
            if ($message instanceof NotificationMessage) {
                return null;
            }

            return ResponseMessageBuilder::fromMessage($message)
                ->error(ResponseError::methodNotFound())
                ->build();
        }

        $cancellation = new DeferredCancellation();
        $method = $this->getHandlerMethod($handler, $message->method);

        if ($message instanceof RequestMessage) {
            $this->cancellations[$message->id] = $cancellation;
        }

        $args = [$message->params, $cancellation];

        try {
            $result = $handler->$method(...$args);
        } catch (Throwable $e) {
            if ($message instanceof NotificationMessage) {
                return null;
            }
        
            return ResponseMessageBuilder::fromMessage($message)
                ->error(ResponseError::fromException($e))
                ->build();
        }

        // We don't need to send a response for notifications
        if ($message instanceof NotificationMessage) {
            return null;
        }

        return ResponseMessageBuilder::fromMessage($message)
            ->result($result)
            ->build();
    }

    /**
     * @param string $id
     * @return void
     */
    public function cancel(string $id): void
    {
        if (!isset($this->cancellations[$id])) {
            return;
        }

        $this->cancellations[$id]->cancel();
        unset($this->cancellations[$id]);
    }

    /**
     * @param Message $message
     * @return bool
     */
    private function isCancellationMessage(Message $message): bool
    {
        return $message instanceof NotificationMessage && $message->method === '$/cancelRequest';
    }

    /**
     * @param MessageHandlerInterface $handler
     * @param string $method
     * @return mixed
     */
    private function getHandlerMethod(MessageHandlerInterface $handler, string $method)
    {
        $methods = $handler->methods();

        if (!array_key_exists($method, $methods)) {
            throw new RuntimeException(sprintf('Method "%s" not found in handler', $method));
        }

        return $methods[$method];
    }
}
