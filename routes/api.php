<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\Api\AppointmentsController;
use App\Http\Controllers\Api\ChatSessionController;
use App\Http\Controllers\Api\ChatMessageController;
use App\Http\Controllers\Api\LawyerRecommendationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LawyerController;

// -------------------------
// Public auth routes
// -------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// -------------------------
// Public Lawyers routes (for booking form)
// -------------------------
Route::get('/lawyers',       [LawyerController::class, 'index']);
Route::get('/lawyers/{id}',  [LawyerController::class, 'show']);

// -------------------------
// Authenticated user routes
// -------------------------
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Appointments - CRUD
    Route::get('/appointments',          [AppointmentsController::class, 'index']);
    Route::post('/appointments',         [AppointmentsController::class, 'store']);
    Route::get('/appointments/{id}',     [AppointmentsController::class, 'show']);
    Route::put('/appointments/{id}',     [AppointmentsController::class, 'update']);
    Route::delete('/appointments/{id}',  [AppointmentsController::class, 'destroy']);

    // Appointment actions
    Route::post('/appointments/{id}/accept',   [AppointmentsController::class, 'accept']);
    Route::post('/appointments/{id}/reject',   [AppointmentsController::class, 'reject']);
    Route::post('/appointments/{id}/cancel',   [AppointmentsController::class, 'cancel']);
    Route::post('/appointments/{id}/complete', [AppointmentsController::class, 'complete']);

    // Role-specific views (you can still check role() inside controller)
    Route::get('/appointments/client', [AppointmentsController::class, 'clientAppointments']);
    Route::get('/appointments/lawyer', [AppointmentsController::class, 'lawyerAppointments']);

    // Chat sessions
    Route::post('/chat/sessions',              [ChatSessionController::class, 'store']);
    Route::get('/chat/sessions',               [ChatSessionController::class, 'index']);
    Route::post('/chat/sessions/{id}/end',     [ChatSessionController::class, 'end']);

    // Chat messages
    Route::post('/chat/messages',                      [ChatMessageController::class, 'store']);
    Route::get('/chat/sessions/{sessionId}/messages',  [ChatMessageController::class, 'index']);

    // Lawyer recommendation
    Route::post('/lawyers/recommend', [LawyerRecommendationController::class, 'recommend']);
    Route::middleware(['auth:sanctum', 'role:client'])->group(function () {
    // client-only routes
    });
    Route::middleware(['auth:sanctum', 'role:lawyer'])->group(function () {
    // lawyer-only routes
    });

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/users', function (Request $request) {
        $users = User::leftJoin('lawyer_profiles', 'users.id', '=', 'lawyer_profiles.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.role',
                'users.status',
                'users.created_at',
                // expose license_no as barNumber for frontend
                'lawyer_profiles.license_no as barNumber'
            )
            ->orderBy('users.created_at', 'desc')
            ->get();

        return response()->json([
            'users' => $users,
        ]);
    });

    Route::post('/admin/lawyers/{user}/approve', function (User $user) {
        if ($user->role !== 'lawyer') {
            return response()->json(['message' => 'Not a lawyer'], 422);
        }

        $user->status = 'approved';
        $user->save();

        return response()->json(['message' => 'Lawyer approved successfully']);
    });
});
Route::middleware(['auth:sanctum', 'role:admin'])->delete('/admin/users/{user}', function (App\Models\User $user) {
    $user->delete();

    return response()->json(['message' => 'User deleted successfully']);
});
});

    

    

