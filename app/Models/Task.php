<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Listing;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'assigned_user_id',
        'listing_id',
        'name',
        'description',
        'file',
        'result_text',
        'result_file',
        'modification_note',
        'status',
        'deadline',
    ];

       protected $casts = [
        'deadline' => 'datetime', // This enables format(), diffForHumans(), etc.
    ];


    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
