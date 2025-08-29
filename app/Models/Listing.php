<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{

    use HasFactory;
    public function scopeFilter($query, array $filters) {
        if (!empty($filters['search'])) {
            $query->where(function ($query) use ($filters) {
                $query->where('title', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('author', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('department', 'like', '%' . $filters['search'] . '%');
            });
        }
    }

    // Relationship To User
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship To Applications
    public function applications()
{
    return $this->hasMany(Application::class, 'listing_id');
}

public function tasks()
{
    return $this->hasMany(Task::class);
}

}
