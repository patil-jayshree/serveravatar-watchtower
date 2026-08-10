<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Agent\AgentTokenStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AgentToken extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'project_id',
        'token_prefix',
        'token_hash',
        'status',
        'last_used_at',
        'revoked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => AgentTokenStatus::class,
        'last_used_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'token_hash',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (AgentToken $token) {
            if (empty($token->uuid)) {
                $token->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the project that owns the token.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Generate a new secure token.
     *
     * @return array{token: string, hash: string, prefix: string}
     */
    public static function generateToken(): array
    {
        $prefix = 'wt_live_';
        $randomPart = Str::random(44);

        return [
            'token' => $prefix . $randomPart,
            'hash' => hash('sha256', $prefix . $randomPart),
            'prefix' => $prefix,
        ];
    }

    /**
     * Verify a raw token against this token's hash.
     */
    public function verify(string $rawToken): bool
    {
        if (! $this->status->isValid()) {
            return false;
        }

        $hash = hash('sha256', $rawToken);

        return hash_equals($this->token_hash, $hash);
    }

    /**
     * Verify the token hash without checking status.
     * Used internally for token lookup before status validation.
     */
    public function verifyHash(string $rawToken): bool
    {
        $hash = hash('sha256', $rawToken);

        return hash_equals($this->token_hash, $hash);
    }

    /**
     * Get the masked token for display.
     */
    public function getMaskedTokenAttribute(): string
    {
        return $this->token_prefix . str_repeat('•', 20);
    }

    /**
     * Mark the token as revoked.
     */
    public function revoke(): void
    {
        $this->update([
            'status' => AgentTokenStatus::Revoked,
            'revoked_at' => now(),
        ]);
    }

    /**
     * Check if token is active.
     */
    public function isActive(): bool
    {
        return $this->status === AgentTokenStatus::Active;
    }

    /**
     * Check if token has been revoked.
     */
    public function isRevoked(): bool
    {
        return $this->status === AgentTokenStatus::Revoked;
    }
}
