<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawyerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'license_number',
        'specialization',
        'experience_years',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

