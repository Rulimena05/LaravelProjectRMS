<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'user_id',
        'outcome',
        'notes',
        'duration_seconds',
    ];

    /**
     * Mendapatkan user (agen) yang membuat log panggilan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan kontak yang berhubungan dengan log panggilan.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function promiseToPay()
    {
        return $this->hasOne(PromiseToPay::class);
    }
}
