<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerPath;
use Illuminate\Http\Request;

class CareerPathAdminController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'required_skills' => ['nullable', 'array'],
            'required_skills.*' => ['integer', 'exists:skills,id'],
        ]);

        $path = CareerPath::query()->create($data);

        return response()->json(['career_path' => $path], 201);
    }

    public function update(Request $request, CareerPath $careerPathId)
    {
        $data = $request->validate([
            'title'           => ['sometimes', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'required_skills' => ['nullable', 'array'],
            'required_skills.*' => ['integer', 'exists:skills,id'],
        ]);

        $careerPathId->update($data);

        return response()->json(['career_path' => $careerPathId->fresh()]);
    }

    public function destroy(CareerPath $careerPathId)
    {
        $careerPathId->delete();

        return response()->json(['message' => 'Career path deleted.']);
    }
}
