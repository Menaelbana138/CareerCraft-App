<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillAdminController extends Controller
{
    public function index()
    {
        return response()->json(Skill::query()->latest('id')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255', 'unique:skills,name'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        $skill = Skill::query()->create($data);

        return response()->json(['skill' => $skill], 201);
    }

    public function update(Request $request, Skill $skillId)
    {
        $data = $request->validate([
            'name'     => ['sometimes', 'string', 'max:255', 'unique:skills,name,' . $skillId->id],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        $skillId->update($data);

        return response()->json(['skill' => $skillId->fresh()]);
    }

    public function destroy(Skill $skillId)
    {
        $skillId->delete();

        return response()->json(['message' => 'Skill deleted.']);
    }
}
