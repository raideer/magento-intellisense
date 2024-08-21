<?php

namespace Raideer\MagentoIntellisense\Server\Rpc;

final class ResponseMessageBuilder
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @var ResponseError|null
     */
    private $error;

    public static function create(): self
    {
        return new self();
    }

    public static function fromMessage(RequestMessage $message)
    {
        return (new self())
            ->id($message->id);
    }

    public function id(string|int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function result(mixed $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function error(ResponseError $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function build(): ResponseMessage
    {
        return new ResponseMessage($this->id, $this->result, $this->error);
    }
}
