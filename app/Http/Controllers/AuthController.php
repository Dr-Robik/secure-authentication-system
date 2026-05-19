<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\VerifyEmailMail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:255',
            'phone' => 'required|string',
            'role' => 'required|in:customer,driver'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $token = Str::random(60);

        $user = User::create([
            'name' => trim($request->name),
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => 'inactive',
            'email_verified_at' => null,
            'email_verification_token' => $token
        ]);

        $link = url("/api/auth/verify-email?email={$user->email}&token={$token}");

        Mail::to($user->email)->send(new VerifyEmailMail($link));

        return response()->json([
            'message' => 'User registered successfully. Please verify your email before login.',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = [
            'email' => strtolower($request->email),
            'password' => $request->password
        ];

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = auth('api')->user();

        if ($user->status !== 'active') {
            auth('api')->logout();
            return response()->json([
                'message' => 'Account is not active'
            ], 403);
        }

        if (!$user->email_verified_at) {
            auth('api')->logout();
            return response()->json([
                'message' => 'Email not verified. Please verify your email first.'
            ], 403);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', strtolower($request->email))
            ->where('email_verification_token', $request->token)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid verification token'
            ], 400);
        }

        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->status = 'active';
        $user->save();

        return response()->json([
            'message' => 'Email verified successfully'
        ]);
    }

    public function resendVerificationEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', strtolower($request->email))->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified'
            ]);
        }

        $token = Str::random(60);

        $user->email_verification_token = $token;
        $user->save();

        $link = url("/api/auth/verify-email?email={$user->email}&token={$token}");

        Mail::to($user->email)->send(new VerifyEmailMail($link));

        return response()->json([
            'message' => 'Verification email sent successfully'
        ]);
    }

    public function me()
    {
        return response()->json([
            'user' => auth('api')->user()
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'access_token' => auth('api')->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ]);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}