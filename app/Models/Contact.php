<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- TAMBAHKAN BARIS INI
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    // TAMBAHKAN PROPERTI INI
    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'status',
        'notes',
        'user_id',
        'campaign_id',
        'task_id',
        'additional_data' // <-- Tambahkan additional_data
    ];
    protected $casts = [
        'additional_data' => 'array',
    ];

    /**
     * Sebuah Contact dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function callbacks()
    {
        return $this->hasMany(Callback::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function phoneNumbers()
    {
        return $this->hasMany(ContactPhoneNumber::class);
    }
}
