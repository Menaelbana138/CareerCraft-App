<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function show(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'user'    => $user,
            'profile' => $user->profile ?? null,
        ]);
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'name'             => ['sometimes', 'string', 'max:255'],
            // Accept either a real file upload or a URL string (Google photo)
            'profile_picture'  => ['sometimes', 'nullable'],
            'resume_text'      => ['sometimes', 'nullable', 'string', 'max:10000'],
            'experience_years' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:50'],
        ]);

        // Handle file upload
        if ($request->hasFile('profile_picture')) {
            $request->validate([
                'profile_picture' => ['image', 'max:2048'], // max 2MB
            ]);

            // Delete old picture if it was stored locally
            if ($user->profile_picture && str_starts_with($user->profile_picture, '/storage/')) {
                $oldPath = str_replace('/storage/', 'public/', $user->profile_picture);
                Storage::delete($oldPath);
            }

            $path = $request->file('profile_picture')
                ->store('profile-pictures', 'public');

            $data['profile_picture'] = Storage::url($path);
        }

        $userFields = array_intersect_key($data, array_flip(['name', 'profile_picture']));
        if (!empty($userFields)) {
            $user->fill($userFields)->save();
        }

        $profileFields = array_intersect_key($data, array_flip(['resume_text', 'experience_years']));
        if (!empty($profileFields)) {
            $user->profile()->updateOrCreate(['user_id' => $user->id], $profileFields);
        }

        return response()->json([
            'user'    => $user->fresh(),
            'profile' => $user->profile()->first(),
        ]);
    }
}
