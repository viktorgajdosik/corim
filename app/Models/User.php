<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Application;
use App\Models\Listing;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'department',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // **Default Value for Department**
    protected $attributes = [
        'department' => 'Student', // ✅ Default department value
    ];

    // **Relationships**
    public function listings() {
        return $this->hasMany(Listing::class, 'user_id');
    }

    public function applications() {
        return $this->hasMany(Application::class, 'user_id');
    }

    public function acceptedApplications() {
        return $this->hasMany(Application::class)->where('accepted', true);
    }

    // **Update User Profile Safely**
    public function safeUpdate(array $attributes) {
        $filteredAttributes = array_intersect_key($attributes, array_flip($this->fillable));

        foreach ($filteredAttributes as $key => $value) {
            $this->$key = $value;
        }

        return $this->save();
    }

    // **Safe Delete Method**
    public function safeDelete() {
        // ✅ Ensure all related data is deleted before deleting user
        $this->applications()->delete();
        $this->listings()->delete();

        return $this->delete();
    }

    public function tasks()
{
    return $this->belongsToMany(Task::class, 'task_participants')
                ->withPivot(['result_text', 'result_file', 'status'])
                ->withTimestamps();
}
}
