<?php

namespace Raideer\MagentoIntellisense\Server\WorkDone;

use Raideer\MagentoIntellisense\Server\Rpc\Client as RpcClient;

final class Client
{
    public function __construct(
        private RpcClient $rpcClient,
    ) {
    }

    /**
     * @param Token $token
     * @return void
     */
    public function create(Token $token)
    {
        $this->rpcClient->request('window/workDoneProgress/create', [
            'token' => (string) $token,
        ]);
    }

    /**
     * @param Token $token
     * @param string $title
     * @param null|string $message
     * @param null|int $percentage
     * @param null|bool $cancellable
     * @return void
     */
    public function begin(Token $token, string $title, ?string $message = null, ?int $percentage = null, ?bool $cancellable = null): void
    {
        $this->notify($token, [
            'kind' => 'begin',
            'title' => $title,
            'message' => $message,
            'percentage' => $percentage,
            'cancellable' => $cancellable,
        ]);
    }

    /**
     * @param Token $token
     * @param null|string $message
     * @param null|int $percentage
     * @param null|bool $cancellable
     * @return void
     */
    public function report(Token $token, ?string $message = null, ?int $percentage = null, ?bool $cancellable = null): void
    {
        $this->notify($token, [
            'kind' => 'report',
            'message' => $message,
            'percentage' => $percentage,
            'cancellable' => $cancellable,
        ]);
    }

    /**
     * @param Token $token
     * @param null|string $message
     * @return void
     */
    public function end(Token $token, ?string $message = null): void
    {
        $this->notify($token, [
            'kind' => 'end',
            'message' => $message,
        ]);
    }

    /**
     * @param Token $token
     * @param array $value
     * @return void
     */
    public function notify(Token $token, array $value)
    {
        $this->rpcClient->notify('$/progress', [
            'token' => (string) $token,
            'value' => $value,
        ]);
    }
}
