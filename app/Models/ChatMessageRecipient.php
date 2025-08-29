<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessageRecipient extends Model
{
    protected $fillable = ['message_id', 'user_id', 'read_at'];
    protected $casts = ['read_at' => 'datetime'];
}
