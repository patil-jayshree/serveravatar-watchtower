<?php

namespace App\Enums\Project;

enum ProjectFramework: string
{
    case LARAVEL = 'laravel';

    /**
     * Get all available frameworks.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the display label for the framework.
     */
    public function label(): string
    {
        return match ($this) {
            self::LARAVEL => 'Laravel',
        };
    }
}
