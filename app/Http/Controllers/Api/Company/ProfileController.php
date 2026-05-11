<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * GET /api/company/profile
     */
    public function show(Request $request)
    {
        /** @var Company $company */
        $company = $request->user();

        return response()->json(['company' => $company]);
    }

    /**
     * PUT /api/company/profile
     */
    public function update(Request $request)
    {
        /** @var Company $company */
        $company = $request->user();

        $data = $request->validate([
            'name'            => ['sometimes', 'string', 'max:255'],
            'profile_picture' => ['sometimes', 'nullable'],
            'password'        => ['sometimes', 'string', 'confirmed', Password::min(8)],
        ]);

        // Handle file upload
        if ($request->hasFile('profile_picture')) {
            $request->validate([
                'profile_picture' => ['image', 'max:2048'],
            ]);

            if ($company->profile_picture && str_starts_with($company->profile_picture, '/storage/')) {
                $oldPath = str_replace('/storage/', 'public/', $company->profile_picture);
                Storage::delete($oldPath);
            }

            $path = $request->file('profile_picture')
                ->store('company-pictures', 'public');

            $data['profile_picture'] = Storage::url($path);
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $company->update($data);

        return response()->json(['company' => $company->fresh()]);
    }
}
