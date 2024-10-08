<?php

namespace Raideer\MagentoIntellisense\Server\Rpc;

use Raideer\MagentoIntellisense\Server\Api\TransportInterface;
use Ramsey\Uuid\Uuid;
use RuntimeException;

final class Client
{
    private const WRITE_CHUNK_SIZE = 256;

    public function __construct(
        private TransportInterface $transport,
        private MessageParser $reader,
    ) {
    }

    /**
     * @param string $method
     * @param array $params
     * @return void
     */
    public function notify(string $method, array $params): void
    {
        $notification = new NotificationMessage($method, $params);

        $this->writeInChunks(
            $this->encode($notification),
        );
    }

    public function request(string $method, array $params): void
    {
        $request = new RequestMessage(Uuid::uuid4()->toString(), $method, $params);

        $this->writeInChunks(
            $this->encode($request),
        );
    }

    /**
     * @param ResponseMessage $response
     * @return void
     * @throws RuntimeException
     */
    public function respond(ResponseMessage $response)
    {
        $this->writeInChunks(
            $this->encode($response),
        );
    }

    /**
     * @param Message $message
     * @return string
     */
    private function encode(Message $message): string
    {
        $data = $this->normalize($message);

        if ($message instanceof ResponseMessage) {
            $data = $this->ensureOnlyResultOrErrorSet($data);
        }

        $body = json_encode($data);
        $bodyLength = strlen($body);

        return "Content-Length: $bodyLength\r\n\r\n$body";
    }

    /**
     * @param mixed $message
     * @return mixed
     */
    private function normalize($message): mixed
    {
        if (is_object($message)) {
            $message = (array) $message;
        }

        if (!is_array($message)) {
            return $message;
        }

        return array_filter(array_map([$this, 'normalize'], $message), function ($value) {
            return $value !== null;
        });
    }

    /**
     * @param array $data 
     * @return array 
     */
    private function ensureOnlyResultOrErrorSet(array $data): array
    {
        if (array_key_exists('error', $data) && array_key_exists('result', $data)) {
            unset($data['result']);
            return $data;
        }

        if (!array_key_exists('error', $data) && !array_key_exists('result', $data)) {
            $data['result'] = null;
        }

        return $data;
    }

    /**
     * @param string $message
     * @return void
     */
    private function writeInChunks(string $message): void
    {
        $length = strlen($message);

        for ($i = 0; $i < $length; $i += self::WRITE_CHUNK_SIZE) {
            $this->transport->write(substr($message, $i, self::WRITE_CHUNK_SIZE));
        }
    }
}
