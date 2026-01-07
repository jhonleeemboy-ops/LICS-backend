<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LawyerRecommendationController extends Controller
{
    public function recommend(Request $request)
    {
        $request->validate([
            'legal_category_id' => 'required|exists:legal_categories,id',
        ]);

        $lawyers = User::where('role', 'lawyer')
            ->whereHas('legalCategories', function ($query) use ($request) {
                $query->where('legal_category_id', $request->legal_category_id);
            })
            ->whereHas('lawyerProfile', function ($query) {
                $query->where('availability', 'available');
            })
            ->with(['lawyerProfile', 'legalCategories'])
            ->get();

        return response()->json([
            'recommended_lawyers' => $lawyers,
        ]);
    }
}
