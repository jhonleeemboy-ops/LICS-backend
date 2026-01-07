<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LawyerController extends Controller
{
    /**
     * Get all active lawyers with their profiles
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'lawyer')
            ->where('status', 'active')
            ->with('lawyerProfile');

        // Filter by specialization if provided
        if ($request->has('specialization')) {
            $query->whereHas('lawyerProfile', function ($q) use ($request) {
                $q->where('specialization', 'like', '%' . $request->specialization . '%');
            });
        }

        // Filter by category if provided
        if ($request->has('category')) {
            $category = $request->category;
            $query->whereHas('lawyerProfile', function ($q) use ($category) {
                $q->where('specialization', 'like', '%' . $category . '%');
            });
        }

        $lawyers = $query->get()->map(function ($lawyer) {
            return [
                'id' => $lawyer->id,
                'name' => $lawyer->name,
                'email' => $lawyer->email,
                'specialization' => $lawyer->lawyerProfile->specialization ?? 'General Practice',
                'license_number' => $lawyer->lawyerProfile->license_number ?? 'N/A',
                'experience_years' => $lawyer->lawyerProfile->experience_years ?? 0,
                'rating' => 4.5, // Default rating - you can add this to LawyerProfile later
                'law_firm' => $lawyer->lawyerProfile->law_firm ?? 'Independent Practice',
            ];
        });

        return response()->json($lawyers);
    }

    /**
     * Get a specific lawyer by ID
     */
    public function show($id)
    {
        $lawyer = User::where('role', 'lawyer')
            ->where('id', $id)
            ->with('lawyerProfile')
            ->firstOrFail();

        return response()->json([
            'id' => $lawyer->id,
            'name' => $lawyer->name,
            'email' => $lawyer->email,
            'specialization' => $lawyer->lawyerProfile->specialization ?? 'General Practice',
            'license_number' => $lawyer->lawyerProfile->license_number ?? 'N/A',
            'experience_years' => $lawyer->lawyerProfile->experience_years ?? 0,
            'rating' => 4.5,
            'law_firm' => $lawyer->lawyerProfile->law_firm ?? 'Independent Practice',
        ]);
    }
}