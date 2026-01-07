<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawyerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'license_no',       // matches migration column
        'specialization',
        'availability',     // optional, if you want mass assignment
    ];

    protected $casts = [
        'availability' => 'decimal:1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
