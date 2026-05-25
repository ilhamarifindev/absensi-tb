<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'class_name',
        'qr_code',
        'photo_url',
        'parent_phone',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
