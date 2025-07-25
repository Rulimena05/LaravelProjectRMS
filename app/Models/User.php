<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status', // <-- Tambahkan ini
        'status_updated_at', // <-- Tambahkan ini
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
        'password' => 'hashed',
        'status_updated_at' => 'datetime', // <-- Tambahkan ini
    ];

    /**
     * Seorang User bisa memiliki banyak Contact.
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function callbacks()
    {
        return $this->hasMany(Callback::class);
    }
}
