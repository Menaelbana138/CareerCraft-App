<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Http\Request;

class RecommendationAdminController extends Controller
{
    /**
     * POST /api/admin/users/{userId}/recommendations
     * Admin manually adds a recommendation for a user.
     */
    public function store(Request $request, User $userId)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type'        => ['nullable', 'string', 'in:career,course,skill'],
        ]);

        $recommendation = $userId->recommendations()->create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'type'        => $data['type'] ?? 'career',
        ]);

        return response()->json(['recommendation' => $recommendation], 201);
    }
}
