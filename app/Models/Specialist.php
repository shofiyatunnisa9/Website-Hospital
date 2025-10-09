<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Specialist extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'name',
        'photo',
        'about',
        'price',

    ];


    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function hospitals()
    {
        return $this->belongsToMany(Hospital::class, 'hospital_specialists');
    }

    public function getPhotoAttribute($value)
    {
        if (!$value) {
            return null; //no image available
        }
        return url(Storage::url($value));
    }
}
