<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class BookingTransaction extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'status',
        'started_at',
        'time_at',
        'sub_total',
        'tax_total',
        'grand_total',
        'proof',
    ];

    protected $casts = [
        'started_at' => 'date',
        'time_at' => 'datetime:H:i'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function getProofAttribute($value)
    {
        if (!$value) {
            return null; //no image available
        }
        return url(Storage::url($value));
    }
}
