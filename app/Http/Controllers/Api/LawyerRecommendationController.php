<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LawyerProfile;
use Illuminate\Http\Request;

class LawyerRecommendationController extends Controller
{
    /**
     * Recommend lawyers based on legal category
     */
    public function recommend(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string',
        ]);

        // Find lawyers whose specialization matches category
        $lawyers = User::where('role', 'lawyer')
            ->whereHas('lawyerProfile', function ($query) use ($request) {
                $query->where('specialization', 'LIKE', '%' . $request->category_name . '%')
                      ->where('availability', 'available');
            })
            ->with('lawyerProfile')
            ->get();

        return response()->json([
            'recommended_lawyers' => $lawyers,
        ]);
    }
}
