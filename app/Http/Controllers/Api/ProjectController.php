<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Get all projects (Admin only)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Admin only.'], 403);
        }

        $projects = Project::with(['admin', 'members.user'])->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'projects' => $projects,
            ],
        ], 200);
    }

    /**
     * Create new project (Admin only)
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Admin only.'], 403);
        }

        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
        ]);

        $project = Project::create([
            'user_id' => $user->id,
            'project_name' => $request->project_name,
            'description' => $request->description,
            'deadline' => $request->deadline,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'project' => $project,
            ],
        ], 201);
    }

    /**
     * Add member to project (Admin only)
     */
    public function addMember(Request $request, $projectId)
    {
        $user = $request->user();
        
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Admin only.'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:leader,developer,designer',
        ]);

        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        // Check if user already in project
        $existingMember = ProjectMember::where('project_id', $projectId)
                                     ->where('user_id', $request->user_id)
                                     ->first();

        if ($existingMember) {
            return response()->json(['message' => 'User already in project'], 409);
        }

        // Update user role if adding as leader
        if ($request->role === 'leader') {
            $memberUser = User::find($request->user_id);
            $memberUser->role = 'leader';
            $memberUser->save();
        }

        $member = ProjectMember::create([
            'project_id' => $projectId,
            'user_id' => $request->user_id,
            'role' => $request->role,
            'joined_at' => now(),
        ]);

        $member->load('user');

        return response()->json([
            'status' => 'success',
            'data' => [
                'member' => $member,
            ],
        ], 201);
    }

    /**
     * Remove member from project (Admin only)
     */
    public function removeMember(Request $request, $projectId, $memberId)
    {
        $user = $request->user();
        
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Admin only.'], 403);
        }

        $member = ProjectMember::where('project_id', $projectId)
                              ->where('id', $memberId)
                              ->first();

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        $member->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Member removed successfully',
        ], 200);
    }

    /**
     * Get project members
     */
    public function getMembers(Request $request, $projectId)
    {
        $user = $request->user();
        
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Admin only.'], 403);
        }

        $members = ProjectMember::where('project_id', $projectId)
                               ->with('user')
                               ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'members' => $members,
            ],
        ], 200);
    }

    /**
     * Get available users to add to project
     */
    public function getAvailableUsers(Request $request)
    {
        $user = $request->user();
        
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Admin only.'], 403);
        }

        // Get users with role 'user' or 'leader' and status 'free'
        $users = User::whereIn('role', ['user', 'leader'])
                    ->where('status', 'free')
                    ->select('id', 'name', 'email', 'role', 'status')
                    ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'users' => $users,
            ],
        ], 200);
    }
}
