<?php

namespace App\Enums\Project;

enum ProjectEnvironment: string
{
    case PRODUCTION = 'production';
    case STAGING = 'staging';
    case DEVELOPMENT = 'development';
    case LOCAL = 'local';

    /**
     * Get all available environments.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the display label for the environment.
     */
    public function label(): string
    {
        return match ($this) {
            self::PRODUCTION => 'Production',
            self::STAGING => 'Staging',
            self::DEVELOPMENT => 'Development',
            self::LOCAL => 'Local',
        };
    }
}
