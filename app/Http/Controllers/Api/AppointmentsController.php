<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AppointmentsController extends Controller
{
    /**
     * Get all appointments (filtered by role)
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'client') {
            $appointments = $user->clientAppointments()
                ->with('lawyer.lawyerProfile')
                ->orderBy('schedule_date', 'desc')
                ->get();
        } elseif ($user->role === 'lawyer') {
            $appointments = $user->lawyerAppointments()
                ->with('client')
                ->orderBy('schedule_date', 'desc')
                ->get();
        } else {
            // Admin sees all
            $appointments = Appointment::with('client', 'lawyer.lawyerProfile')
                ->orderBy('schedule_date', 'desc')
                ->get();
        }

        return response()->json($this->formatAppointments($appointments));
    }

    /**
     * CLIENT creates appointment
     */
    public function store(Request $request)
    {
        $request->validate([
            'lawyer_id' => 'nullable|exists:users,id',
            'category' => 'required|string',
            'date' => 'required|date',
            'time' => 'required|string',
            'client_name' => 'required|string',
            'client_phone' => 'required|string',
            'client_email' => 'required|email',
            'consultation_type' => 'required|in:in-person,online,phone',
            'notes' => 'nullable|string',
        ]);

        // Combine date and time
        $scheduleDateTime = $request->date . ' ' . $request->time;

        $appointment = Appointment::create([
            'client_id' => Auth::id(),
            'lawyer_id' => $request->lawyer_id,
            'category' => $request->category,
            'schedule_date' => $scheduleDateTime,
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'client_email' => $request->client_email,
            'consultation_type' => $request->consultation_type,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        // Load relationships before returning
        $appointment->load('lawyer.lawyerProfile', 'client');

        return response()->json($this->formatAppointment($appointment), 201);
    }

    /**
     * Show a single appointment
     */
    public function show($id)
    {
        $appointment = Appointment::with('client', 'lawyer.lawyerProfile')->findOrFail($id);

        // Check authorization
        $user = Auth::user();
        if ($user->role === 'client' && $appointment->client_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        if ($user->role === 'lawyer' && $appointment->lawyer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($this->formatAppointment($appointment));
    }

    /**
     * Update appointment
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        // Check authorization
        $user = Auth::user();
        if ($user->role === 'client' && $appointment->client_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'schedule_date' => 'sometimes|date',
            'time' => 'sometimes|string',
            'notes' => 'sometimes|string',
            'status' => 'sometimes|in:pending,accepted,rejected,cancelled,completed',
        ]);

        if ($request->has('date') && $request->has('time')) {
            $appointment->schedule_date = $request->date . ' ' . $request->time;
        }

        if ($request->has('notes')) {
            $appointment->notes = $request->notes;
        }

        if ($request->has('status')) {
            $appointment->status = $request->status;
        }

        $appointment->save();
        $appointment->load('client', 'lawyer.lawyerProfile');

        return response()->json($this->formatAppointment($appointment));
    }

    /**
     * Delete appointment
     */
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        // Check authorization
        $user = Auth::user();
        if ($user->role === 'client' && $appointment->client_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted successfully']);
    }

    /**
     * LAWYER accepts appointment
     */
    public function accept($id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = Auth::user();

        // If appointment has no lawyer assigned, assign current lawyer
        if (!$appointment->lawyer_id) {
            $appointment->lawyer_id = $user->id;
        }

        // Check authorization
        if ($appointment->lawyer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appointment->status = 'accepted';
        $appointment->accepted_at = now();
        $appointment->save();

        $appointment->load('client', 'lawyer.lawyerProfile');

        return response()->json([
            'message' => 'Appointment accepted successfully',
            'appointment' => $this->formatAppointment($appointment)
        ]);
    }

    /**
     * LAWYER rejects appointment
     */
    public function reject($id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = Auth::user();

        // Check authorization
        if ($appointment->lawyer_id && $appointment->lawyer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appointment->status = 'rejected';
        $appointment->rejected_at = now();
        $appointment->save();

        $appointment->load('client', 'lawyer.lawyerProfile');

        return response()->json([
            'message' => 'Appointment rejected',
            'appointment' => $this->formatAppointment($appointment)
        ]);
    }

    /**
     * CLIENT cancels appointment
     */
    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = Auth::user();

        // Check authorization
        if ($appointment->client_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appointment->status = 'cancelled';
        $appointment->cancelled_at = now();
        $appointment->save();

        $appointment->load('client', 'lawyer.lawyerProfile');

        return response()->json([
            'message' => 'Appointment cancelled',
            'appointment' => $this->formatAppointment($appointment)
        ]);
    }

    /**
     * Mark appointment as completed
     */
    public function complete($id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = Auth::user();

        // Only lawyer can mark as completed
        if ($user->role !== 'lawyer' || $appointment->lawyer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appointment->status = 'completed';
        $appointment->completed_at = now();
        $appointment->save();

        $appointment->load('client', 'lawyer.lawyerProfile');

        return response()->json([
            'message' => 'Appointment marked as completed',
            'appointment' => $this->formatAppointment($appointment)
        ]);
    }

    /**
     * CLIENT views own appointments
     */
    public function clientAppointments()
    {
        $user = Auth::user();
        $appointments = $user->clientAppointments()
            ->with('lawyer.lawyerProfile')
            ->orderBy('schedule_date', 'desc')
            ->get();
        
        return response()->json($this->formatAppointments($appointments));
    }

    /**
     * LAWYER views appointments
     */
    public function lawyerAppointments()
    {
        $user = Auth::user();
        
        // Show appointments assigned to this lawyer OR unassigned appointments
        $appointments = Appointment::where(function ($query) use ($user) {
                $query->where('lawyer_id', $user->id)
                      ->orWhereNull('lawyer_id');
            })
            ->with('client')
            ->orderBy('schedule_date', 'desc')
            ->get();
        
        return response()->json($this->formatAppointments($appointments));
    }

    /**
     * Format single appointment for frontend
     */
    private function formatAppointment($appointment)
    {
        $scheduleDate = \Carbon\Carbon::parse($appointment->schedule_date);
        
        return [
            'id' => $appointment->id,
            'lawyer' => $appointment->lawyer ? $appointment->lawyer->name : 'Pending Assignment',
            'lawyerEmail' => $appointment->lawyer ? $appointment->lawyer->email : null,
            'category' => $appointment->category,
            'date' => $scheduleDate->format('Y-m-d'),
            'time' => $scheduleDate->format('g:i A'),
            'status' => ucfirst($appointment->status),
            'notes' => $appointment->notes,
            'consultationType' => $appointment->consultation_type,
            'location' => 'To be confirmed',
            'contact' => 'To be provided',
            'clientName' => $appointment->client_name,
            'clientEmail' => $appointment->client_email,
            'clientPhone' => $appointment->client_phone,
            'createdAt' => $appointment->created_at->toISOString(),
            'createdBy' => $appointment->client_email,
        ];
    }

    /**
     * Format multiple appointments
     */
    private function formatAppointments($appointments)
    {
        return $appointments->map(function ($appointment) {
            return $this->formatAppointment($appointment);
        });
    }
}