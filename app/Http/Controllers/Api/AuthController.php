<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if(User::where('email', $request->email)->exists()) {
            return response()->json(['message' => 'Email already exists'], 409);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user',
                'status' => 'free',
            ]);

            if (!$user) {
                return response()->json(['message' => 'User creation failed'], 500);
            } else {
                $token = $user->createToken('auth_token')->plainTextToken;
            }

            return response()->json([
                'status' => 'success',
                'access_token' => $token,
                'data' => [
                    'user' => $user,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json(['message' => 'Registration failed'], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if(User::where('email', $request->email)->doesntExist()) {
            return response()->json(['message' => 'Email does not exist'], 404);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'data' => [
                'user' => $user,
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }
            
            // Delete current access token only (or all tokens if you prefer)
            $request->user()->currentAccessToken()->delete();
            
            // To delete all tokens for the user, use:
            // $user->tokens()->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json(['message' => 'Logout failed'], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => $user,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Profile error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to get profile'], 500);
        }
    }

    public function googleLogin(Request $request)
    {
        try {
            $request->validate([
                'id_token' => 'required|string',
            ]);

            $idToken = $request->id_token;
            
            Log::info('Google Login Attempt', ['token_length' => strlen($idToken)]);
            
            // Try to decode JWT token directly (for development/testing)
            // In production, you should verify with Google API
            try {
                // Split JWT token
                $tokenParts = explode('.', $idToken);
                if (count($tokenParts) !== 3) {
                    return response()->json(['message' => 'Invalid token format'], 400);
                }
                
                // Decode payload (middle part)
                $payload = json_decode(base64_decode(strtr($tokenParts[1], '-_', '+/')), true);
                
                if (!$payload) {
                    return response()->json(['message' => 'Cannot decode token'], 400);
                }
                
                Log::info('Decoded Token Payload', ['payload' => $payload]);
                
            } catch (\Exception $e) {
                Log::error('Token Decode Error', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Invalid token'], 400);
            }

            $email = $payload['email'] ?? null;
            $name = $payload['name'] ?? 'Google User';
            $googleId = $payload['sub'] ?? null;
            $avatar = $payload['picture'] ?? null;

            if (!$email || !$googleId) {
                return response()->json(['message' => 'Invalid token data - missing email or sub'], 400);
            }

            // Verify email is verified
            if (!isset($payload['email_verified']) || !$payload['email_verified']) {
                return response()->json(['message' => 'Email not verified'], 400);
            }

            // Check if user exists
            $user = User::where('email', $email)->first();

            if ($user) {
                // Update google_id and avatar if not set
                if (!$user->google_id) {
                    $user->google_id = $googleId;
                }
                if (!$user->avatar && $avatar) {
                    $user->avatar = $avatar;
                }
                $user->save();
                
                Log::info('Existing user logged in', ['user_id' => $user->id]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                    'password' => Hash::make(uniqid()), // Random password for Google users
                    'role' => 'user',
                    'status' => 'free',
                ]);
                
                Log::info('New user created', ['user_id' => $user->id]);
            }

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('Google Login Success', ['user_id' => $user->id, 'email' => $email]);

            return response()->json([
                'status' => 'success',
                'access_token' => $token,
                'data' => [
                    'user' => $user,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            return response()->json(['message' => 'Google login failed: ' . $e->getMessage()], 500);
        }
    }
}