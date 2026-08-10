<?php

declare(strict_types=1);

namespace App\Enums\Agent;

enum AgentTokenStatus: string
{
    case Active = 'active';
    case Revoked = 'revoked';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Revoked => 'Revoked',
        };
    }

    public function isValid(): bool
    {
        return $this === self::Active;
    }
}
