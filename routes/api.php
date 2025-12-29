<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppointmentsController;
use App\Http\Controllers\Api\ChatSessionController;
use App\Http\Controllers\Api\ChatMessageController;
use App\Http\Controllers\Api\LawyerRecommendationController;
use App\Http\Controllers\Api\AuthController;
  
  // Public Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Appointment Routes
    Route::middleware('auth:sanctum')->group(function () {
    Route::post('/appointments', [AppointmentsController::class, 'store']);
    Route::get('/appointments/client', [AppointmentsController::class, 'clientAppointments']);
    Route::get('/appointments/lawyer', [AppointmentsController::class, 'lawyerAppointments']);

    // Chat session routes
    Route::post('/chat/sessions', [ChatSessionController::class, 'store']);
    Route::get('/chat/sessions', [ChatSessionController::class, 'index']);
    Route::post('/chat/sessions/{id}/end', [ChatSessionController::class, 'end']);

    // Chat messages routes
    Route::post('/chat/messages', [ChatMessageController::class, 'store']);
    Route::get('/chat/sessions/{sessionId}/messages', [ChatMessageController::class, 'index']);
    });


    // Lawyer Recommendation Route
    Route::middleware('auth:sanctum')->post(
    '/lawyers/recommend',
    [LawyerRecommendationController::class, 'recommend']
    );

    // client routes
    Route::middleware(['auth:sanctum', 'role:client'])->group(function () {
        
    });
    // lawyer routes
    Route::middleware(['auth:sanctum', 'role:lawyer'])->group(function () {
        
    });

    Route::post('/test', function() {
    return response()->json(['message' => 'API is working']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});