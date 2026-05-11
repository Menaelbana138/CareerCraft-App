<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseAdminController extends Controller
{
    public function index()
    {
        return response()->json(Course::query()->with('skill:id,name')->latest('id')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'url'      => ['nullable', 'url', 'max:500'],
            'platform' => ['nullable', 'string', 'max:100'],
            'skill_id' => ['required', 'integer', 'exists:skills,id'],
        ]);

        $course = Course::query()->create($data);

        return response()->json(['course' => $course->load('skill:id,name')], 201);
    }

    public function update(Request $request, Course $courseId)
    {
        $data = $request->validate([
            'title'    => ['sometimes', 'string', 'max:255'],
            'url'      => ['nullable', 'url', 'max:500'],
            'platform' => ['nullable', 'string', 'max:100'],
            'skill_id' => ['sometimes', 'integer', 'exists:skills,id'],
        ]);

        $courseId->update($data);

        return response()->json(['course' => $courseId->fresh()->load('skill:id,name')]);
    }

    public function destroy(Course $courseId)
    {
        $courseId->delete();

        return response()->json(['message' => 'Course deleted.']);
    }
}
