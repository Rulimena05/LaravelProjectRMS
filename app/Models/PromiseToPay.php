<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromiseToPay extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'call_log_id', 
        'user_id', 
        'contact_id', 
        'ptp_date', 
        'ptp_amount', 
        'status'
    ];

    /**
     * Mendapatkan user (agen) yang membuat PTP.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}