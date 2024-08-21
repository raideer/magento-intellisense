<?php

namespace Raideer\MagentoIntellisense\Server\Rpc;

final class ResponseError
{
    public const PARSE_ERROR = -32700;
    public const INVALID_REQUEST = -32600;
    public const METHOD_NOT_FOUND = -32601;
    public const INVALID_PARAMS = -32602;
    public const INTERNAL_ERROR = -32603;

    public function __construct(
        public int $code,
        public string $message,
        public ?string $data = null,
    ) {
    }

    public static function methodNotFound($message = "Method not found", $data = null)
    {
        return new self(
            self::METHOD_NOT_FOUND,
            $message,
            $data,
        );
    }

    public static function fromException(\Throwable $e)
    {
        return new self(
            self::INTERNAL_ERROR,
            $e->getMessage(),
            $e->getTraceAsString(),
        );
    }
}
