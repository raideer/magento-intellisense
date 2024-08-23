<?php

namespace Raideer\MagentoIntellisense\Server\WorkDone;

use Ramsey\Uuid\Uuid;

final class Token
{
    /**
     * @var string
     */
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function __toString(): string
    {
        return $this->token;
    }

    /**
     * @return Token
     */
    public static function generate(): self
    {
        return new self((string) Uuid::uuid4());
    }
}
