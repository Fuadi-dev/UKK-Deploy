<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check if login is email or username
        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        
        if (Auth::attempt([$loginField => $credentials['login'], 'password' => $credentials['password']], $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Check if user has role (pastikan role tidak null)
            if (Auth::user()->role && Auth::user()->role !== null) {
                return redirect()->intended('/dashboard');
            } else {
                return redirect()->route('role.select');
            }
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role adalah 'user'
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // Google OAuth Methods
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Update google_id and avatar if not set
                $updateData = [];
                if (!$user->google_id) {
                    $updateData['google_id'] = $googleUser->getId();
                }
                if (!$user->avatar && $googleUser->getAvatar()) {
                    $updateData['avatar'] = $googleUser->getAvatar();
                }
                
                if (!empty($updateData)) {
                    User::where('id', $user->id)->update($updateData);
                    $user->refresh(); // Refresh the model to get updated data
                }
                
                Auth::login($user);
                
                // Debug log
                Log::info('Existing user login via Google', [
                    'user_id' => $user->id,
                    'role' => $user->role,
                ]);
                
                // Langsung redirect ke dashboard karena semua user pasti punya role
                return redirect()->intended('/dashboard');
            } else {
                // Create new user with default role 'user'
                $user = User::create([
                    'avatar' => $googleUser->getAvatar(),
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(uniqid()), // Random password for Google users
                    'role' => 'user', // Default role adalah 'user'
                ]);
                
                Auth::login($user);
                
                // Debug log
                Log::info('New user created via Google', [
                    'user_id' => $user->id,
                    'role' => $user->role,
                ]);
                
                // Langsung redirect ke dashboard
                return redirect()->intended('/dashboard');
            }
            
        } catch (\Exception $e) {
            Log::error('Google OAuth Error', ['error' => $e->getMessage()]);
            return redirect()->route('login')->withErrors(['error' => 'Unable to login with Google. Please try again.']);
        }
    }
}
