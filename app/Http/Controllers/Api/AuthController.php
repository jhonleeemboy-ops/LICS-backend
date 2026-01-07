<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClientProfile;
use App\Models\LawyerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user (client or lawyer) + profile.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users',
            'password'              => 'required|string|min:6|confirmed',
            'role'                  => 'required|in:client,lawyer', // no admin register via API

            // Client profile
            'phone'                 => 'required_if:role,client|string|max:20',
            'address'               => 'required_if:role,client|string',

            // Lawyer profile
            'specialization'        => 'required_if:role,lawyer|string|max:255',
            'license_no'            => 'required_if:role,lawyer|string|max:255|unique:lawyer_profiles,license_no',
            'availability'          => 'nullable|numeric|min:0|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Create base user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            // ✅ lawyers start as pending, clients as approved
            'status'   => $request->role === 'lawyer' ? 'pending' : 'approved',
        ]);

        // Create role-specific profile
        if ($user->role === 'client') {
            ClientProfile::create([
                'user_id' => $user->id,
                'phone'   => $request->phone,
                'address' => $request->address,
            ]);
        } elseif ($user->role === 'lawyer') {
            LawyerProfile::create([
                'user_id'       => $user->id,
                'specialization'=> $request->specialization,
                'license_no'    => $request->license_no,
                'availability'  => $request->availability,
            ]);
        }

        // ✅ Only auto-login CLIENTS
        $token = null;
        if ($user->role === 'client') {
            $token = $user->createToken('auth_token')->plainTextToken;
        }

        return response()->json([
            'message' => $user->role === 'lawyer'
                ? 'Application submitted successfully! Your account will be activated after admin review.'
                : 'Registration successful',
            'user'   => $user->load('clientProfile', 'lawyerProfile'),
            'token'  => $token,
        ], 201);
    }

    /**
     * Login user.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        // ✅ Block pending / rejected lawyers
        if ($user->role === 'lawyer' && $user->status !== 'approved') {
            return response()->json([
                'message' => 'Your lawyer account is pending admin approval. Please wait for activation.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user->load('clientProfile', 'lawyerProfile'),
            'token' => $token,
        ]);
    }

    /**
     * Return current authenticated user.
     */
    public function me(Request $request)
    {
        return response()->json(
            $request->user()->load('clientProfile', 'lawyerProfile')
        );
    }

    /**
     * Logout (revoke current token).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }
}
