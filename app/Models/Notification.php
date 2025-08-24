<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'body', 'url', 'seen_at',
    ];

    protected $casts = [
        'seen_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Scope: only unseen notifications */
    public function scopeUnseen(Builder $query): Builder
    {
        return $query->whereNull('seen_at');
    }

    /** Small helper to create a notification (renamed from push) */
    public static function deliver(
        int $userId,
        string $title,
        ?string $body = null,
        ?string $url = null,
        ?string $type = null
    ): self {
        return static::create([
            'user_id' => $userId,
            'title'   => $title,
            'body'    => $body,
            'url'     => $url,
            'type'    => $type,
        ]);
    }

    /** Optional: mark as seen */
    public function markSeen(): void
    {
        $this->update(['seen_at' => now()]);
    }
}
