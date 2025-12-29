<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Models\User;


class AppointmentsController extends Controller
{
    // CLIENT creates appointment
    public function store(Request $request)
    {
        $request->validate([
            'lawyer_id' => 'required|exists:users,id',
            'schedule_date' => 'required|date',
        ]);

        $appointment = Appointment::create([
            'client_id' => Auth::id(),
            'lawyer_id' => $request->lawyer_id,
            'schedule_date' => $request->schedule_date,
            'status' => 'pending',
        ]);

        return response()->json($appointment, 201);
    }

    // CLIENT views own appointments
    public function clientAppointments()
{
    /** @var User $user */
    $user = Auth::user();

    return $user->clientAppointments()->with('lawyer')->get();
}

    public function lawyerAppointments()
    {
    /** @var User $user */
            $user = Auth::user();

    return $user->lawyerAppointments()->with('client')->get();
}

    }
