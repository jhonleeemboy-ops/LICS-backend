<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\ClientProfile;
use App\Models\LawyerProfile;
use App\Models\Appointment;
use App\Models\ChatSession;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 1:1 Client Profile
    public function clientProfile()
    {
        return $this->hasOne(ClientProfile::class);
    }

    // 1:1 Lawyer Profile
    public function lawyerProfile()
    {
        return $this->hasOne(LawyerProfile::class);
    }

    // 1:N Appointments as Client
    public function clientAppointments()
    {
        return $this->hasMany(Appointment::class, 'client_id');
    }

    // 1:N Appointments as Lawyer
    public function lawyerAppointments()
    {
        return $this->hasMany(Appointment::class, 'lawyer_id');
    }

    // 1:N Chat Sessions
    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class);
    }
}
