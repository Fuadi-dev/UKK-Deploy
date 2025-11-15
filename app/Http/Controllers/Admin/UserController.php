<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProjectMember;
use App\Services\ProjectStatusService;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display user profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('pages.admin.users.profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = User::where('id', Auth::id())->first();

        // Prevent Google users from updating their profile
        if ($user->google_id) {
            return back()->with('error', 'Google account users cannot update their profile. Please manage your account through Google.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // If password change is requested, verify current password
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
        }

        // Update user information
        $user->name = $request->name;
        $user->email = Auth::user()->email;
        
        // Handle avatar upload to Supabase
        if ($request->hasFile('avatar')) {
            $supabaseStorage = new SupabaseStorageService();
            
            // Delete old avatar from Supabase if exists and is not a Google avatar URL
            if ($user->avatar && !str_contains($user->avatar, 'googleusercontent.com')) {
                $supabaseStorage->delete($user->avatar);
            }
            
            // Upload new avatar to Supabase
            $avatarPath = $supabaseStorage->upload($request->file('avatar'), 'avatars');
            
            if ($avatarPath) {
                $user->avatar = $avatarPath;
            } else {
                return back()->with('error', 'Failed to upload avatar. Please try again.');
            }
        }
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Display user management page (Admin only)
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Run project status update before displaying user list
        try {
            ProjectStatusService::updateProjectAndUserStatus();
        } catch (\Exception $e) {
            // Log error but don't break the page
            Log::error('Error updating project status: ' . $e->getMessage());
        }

        $search = $request->get('search');
        $role = $request->get('role');
        $sortBy = $request->get('sort_by', 'name');
        $sortDir = $request->get('sort_dir', 'asc');

        $users = User::query();

        if ($search) {
            $users->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role && $role !== 'all') {
            $users->where('role', $role);
        }

        $users = $users->orderBy($sortBy, $sortDir)->paginate(12);

        return view('pages.admin.users.user', compact('users', 'search', 'role', 'sortBy', 'sortDir'));
    }

    /**
     * Store new user (Admin only)
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,leader,user',
            'status' => 'required|in:working,free',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return back()->with('success', 'User created successfully!');
    }

    /**
     * Update user (Admin only)
     */
    public function update(Request $request, User $user)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,leader,user',
            'status' => 'required|in:working,free',
            'password' => 'nullable|string|min:8',
        ]);

        // Check if user is trying to change status from working to free
        if ($user->status === 'working' && $request->status === 'free') {
            // Check if user is currently assigned to any project
            $isInProject = ProjectMember::where('user_id', $user->id)->exists();
            
            if ($isInProject) {
                return back()->with('error', 'Failed! User is already working in other project and cannot be set to free status.');
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return back()->with('success', 'User updated successfully!');
    }

    /**
     * Delete user (Admin only)
     */
    public function destroy(User $user)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully!');
    }
}
