<?php

namespace App\Enums;

enum OrganizationRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';

    /**
     * Get the role label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Owner',
            self::Admin => 'Admin',
            self::Member => 'Member',
        };
    }

    /**
     * Check if this role has admin privileges.
     */
    public function isAdmin(): bool
    {
        return match ($this) {
            self::Owner, self::Admin => true,
            self::Member => false,
        };
    }

    /**
     * Check if this role is the owner.
     */
    public function isOwner(): bool
    {
        return $this === self::Owner;
    }

    /**
     * Check if the role can manage members.
     */
    public function canManageMembers(): bool
    {
        return match ($this) {
            self::Owner, self::Admin => true,
            self::Member => false,
        };
    }

    /**
     * Check if the role can manage organization settings.
     */
    public function canManageSettings(): bool
    {
        return match ($this) {
            self::Owner, self::Admin => true,
            self::Member => false,
        };
    }

    /**
     * Check if the role can delete the organization.
     */
    public function canDelete(): bool
    {
        return $this === self::Owner;
    }
}
