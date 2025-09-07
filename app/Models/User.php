<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Application;
use App\Models\Listing;
use App\Models\Task;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'organization',
        'department',
        'password',
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'deactivated_at'    => 'datetime',
        'banned_at'         => 'datetime',
    ];

    // Accessors for convenience in Blade
    public function getIsBannedAttribute(): bool      { return !is_null($this->banned_at); }
    public function getIsDeactivatedAttribute(): bool { return !is_null($this->deactivated_at); }

    // Optional helpers
    public function isBanned(): bool      { return (bool) $this->banned_at; }
    public function isDeactivated(): bool { return (bool) $this->deactivated_at; }
    public function isActive(): bool      { return ! $this->isBanned() && ! $this->isDeactivated(); }

    protected $attributes = [
        'department' => 'Student',
    ];

    // Relationships
    public function listings()              { return $this->hasMany(Listing::class, 'user_id'); }
    public function applications()          { return $this->hasMany(Application::class, 'user_id'); }
    public function acceptedApplications()  { return $this->hasMany(Application::class)->where('accepted', true); }
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_participants')
            ->withPivot(['result_text', 'result_file', 'status'])
            ->withTimestamps();
    }

    // Safe update
    public function safeUpdate(array $attributes) {
        $filtered = array_intersect_key($attributes, array_flip($this->fillable));
        foreach ($filtered as $k => $v) $this->$k = $v;
        return $this->save();
    }

    // NB: consider NOT deleting related data in production
    public function safeDelete() {
        $this->applications()->delete();
        $this->listings()->delete();
        return $this->delete();
    }
}
