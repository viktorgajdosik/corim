<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChatMessage extends Model
{
    protected $fillable = [
        'listing_id', 'user_id', 'body', 'is_broadcast',
    ];

    protected $casts = [
        'is_broadcast' => 'boolean',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_message_recipients', 'message_id', 'user_id')
            ->withTimestamps()
            ->withPivot('read_at');
    }
}
