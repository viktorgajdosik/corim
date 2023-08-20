<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'department',
        'password',
    ];
        // Explicitly define column names for attributes
    protected $attributes = [
        'department' => '', // Default value
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationship To Listings
    public function listings() {
        return $this->hasMany(Listing::class, 'user_id');
    }

    // Relationship To Applications
    public function applications()
{
    return $this->hasMany(Application::class, 'user_id');
}

public function acceptedApplications() {
    return $this->hasMany(Application::class)->where('accepted', true);
}

}
