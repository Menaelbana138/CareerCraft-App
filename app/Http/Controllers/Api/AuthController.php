<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Services\GoogleIdTokenService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Unified register: type = user|company
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:user,company'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $type = $data['type'];

        if ($type === 'company') {
            $request->validate(['email' => ['unique:companies,email']]);
            $company = Company::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            $token = $company->createToken('company-api')->plainTextToken;
            return response()->json(['token' => $token, 'company' => $company], 201);
        }

        $request->validate(['email' => ['unique:users,email']]);
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
        ]);
        $token = $user->createToken('mobile')->plainTextToken;
        event(new Registered($user));
        return response()->json(['token' => $token, 'user' => $user], 201);
    }

    /**
     * Unified login: type = user|company
     *
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Login and get Sanctum token",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="type", type="string", enum={"user","company"}, example="user")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Token returned"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:user,company'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($data['type'] === 'company') {
            $company = Company::query()->where('email', $data['email'])->first();
            if (!$company || !Hash::check($data['password'], $company->password)) {
                throw ValidationException::withMessages(['email' => ['Invalid credentials.']]);
            }
            $token = $company->createToken('company-api')->plainTextToken;
            return response()->json(['token' => $token, 'company' => $company]);
        }

        $user = User::query()->where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['Invalid credentials.']]);
        }
        if ($user->suspended_at) {
            return response()->json(['message' => 'Account suspended.'], 403);
        }
        $token = $user->createToken('mobile')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function googleLogin(Request $request, GoogleIdTokenService $google)
    {
        $data = $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        $result = $google->verify($data['id_token']);
        if (!$result['ok']) {
            return response()->json(['message' => $result['error']], 422);
        }

        $payload = $result['payload'];
        $email = $payload['email'] ?? null;
        if (!$email) {
            return response()->json(['message' => 'Google token missing email.'], 422);
        }

        $name = $payload['name'] ?? ($payload['given_name'] ?? 'User');
        $picture = $payload['picture'] ?? null;

        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(Str::random(32)),
                'role' => 'user',
                'profile_picture' => $picture,
            ]
        );

        if (!$user->profile_picture && $picture) {
            $user->forceFill(['profile_picture' => $picture])->save();
        }

        if ($user->suspended_at) {
            return response()->json(['message' => 'Account suspended.'], 403);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * GET /api/auth/verify-email/{id}/{hash}
     * The link sent to the user's inbox calls this endpoint.
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $user->markEmailAsVerified();

        return response()->json(['message' => 'Email verified successfully.']);
    }

    /**
     * POST /api/auth/resend-verification
     * Resend the verification email for the authenticated user.
     */
    public function resendVerification(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email resent.']);
    }

    /**
     * POST /api/auth/forgot-password  { email }
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)], 422);
        }

        return response()->json(['message' => __($status)]);
    }

    /**
     * POST /api/auth/reset-password  { token, email, password, password_confirmation }
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->tokens()->delete(); // invalidate all existing tokens
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)], 422);
        }

        return response()->json(['message' => __($status)]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout and revoke current token",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Logged out successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function logout(Request $request)
    {
        $token = $request->user()?->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return response()->json(['message' => 'Logged out']);
    }
}

