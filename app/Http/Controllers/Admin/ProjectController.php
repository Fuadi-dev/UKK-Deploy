<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectMember;
use App\Services\ProjectStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function getProjects(Request $request)
    {
        $perPage = $request->get('per_page', 10); 
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'project_name');
        $sortDir = $request->get('sort_dir', 'asc');

        $user = Auth::user();
        
        // Get projects based on user role
        if ($user->role === 'admin') {
            // Admin can see all projects
            $query = Project::with(['user', 'members.user']);
        } elseif ($user->role === 'leader') {
            // Leader can see projects they created or are assigned to as leader
            $query = Project::where('user_id', $user->id)
                ->orWhereHas('members', function($q) use ($user) {
                    $q->where('user_id', $user->id)->where('role', 'leader');
                })
                ->with(['user', 'members.user']);
        } else {
            // Regular users can only see projects they're assigned to
            $query = Project::whereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['user', 'members.user']);
        }

        // Search by project name and description
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('project_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting data
        $query->orderBy($sortBy, $sortDir);

        // Pagination dengan parameter dinamis
        $projects = $query->paginate($perPage)->withQueryString();

        return view('pages.admin.projects.projects', compact('projects', 'search', 'sortBy', 'sortDir', 'perPage'));
    }

    public function show($slug)
    {
        $user = Auth::user();
        
        // Get project with relationships
        $project = Project::with(['user', 'members.user', 'boards.cards'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Check if user has permission to view this project
        if ($user->role !== 'admin' && 
            $project->user_id != $user->id && 
            !$project->members->where('user_id', $user->id)->count()) {
            abort(403, 'Unauthorized access to this project.');
        }

        return view('pages.admin.projects.detail', compact('project'));
    }

    public function deleteProject($id)
    {
        $user = Auth::user();
        $project = Project::findOrFail($id);

        // Check if user has permission to delete
        if ($user->role !== 'admin' && $project->user_id != $user->id) {
            return back()->with('error', 'You do not have permission to delete this project.');
        }

        DB::beginTransaction();
        
        try {
            // Get all member IDs before deletion
            $memberIds = $project->members()->pluck('user_id')->toArray();
            
            // Delete project members
            $project->members()->delete();
            
            // Update user status to 'free' if they have no other active projects
            foreach ($memberIds as $memberId) {
                $hasOtherProjects = ProjectMember::where('user_id', $memberId)
                    ->whereHas('project', function($q) {
                        $q->whereIn('status', ['active', 'on_hold']);
                    })
                    ->exists();
                
                if (!$hasOtherProjects) {
                    User::where('id', $memberId)->update(['status' => 'free']);
                }
            }
            
            // Soft delete project (cascade will handle boards and cards)
            $project->delete();
            
            DB::commit();
            Log::info('Project deleted successfully', ['project_id' => $id, 'user_id' => $user->id]);
            
            return redirect('projects')->with('success', 'Project deleted successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete project', ['project_id' => $id, 'error' => $e->getMessage()]);
            
            return back()->with('error', 'Failed to delete project. Please try again.');
        }
    }

    public function edit($slug)
    {
        $user = Auth::user();
        $project = Project::with(['user', 'members.user'])->where('slug', $slug)->firstOrFail();

        // Check if user has permission to edit
        if ($user->role !== 'admin' && $project->user_id != $user->id) {
            abort(403, 'You do not have permission to edit this project.');
        }

        return view('pages.admin.projects.edit', compact('project'));
    }

    public function update(Request $request, $slug)
    {
        $user = Auth::user();
        $project = Project::where('slug', $slug)->firstOrFail();

        // Check if user has permission to update
        if ($user->role !== 'admin' && $project->user_id != $user->id) {
            return back()->with('error', 'You do not have permission to update this project.');
        }

        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'nullable|in:active,on_hold,completed,cancelled',
        ]);

        $project->update([
            'project_name' => $request->project_name,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'slug' => Str::slug($request->project_name),
            'status' => $request->status ?? $project->status,
        ]);

        return redirect()->route('projects.show', $project->slug)->with('success', 'Project updated successfully.');
    }

    public function create()
    {
        $user = Auth::user();
        
        // Check if user has permission to create
        if ($user->role !== 'admin' && $user->role !== 'leader') {
            abort(403, 'You do not have permission to create projects.');
        }

        return view('pages.admin.projects.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has permission to create
        if ($user->role !== 'admin' && $user->role !== 'leader') {
            return back()->with('error', 'You do not have permission to create projects.');
        }

        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'leader_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:active,on_hold',
        ]);

        DB::beginTransaction();
        
        try {
            // Create project
            $project = Project::create([
                'project_name' => $request->project_name,
                'description' => $request->description,
                'deadline' => $request->deadline,
                'user_id' => $user->id,
                'slug' => Str::slug($request->project_name),
                'status' => $request->status ?? 'active',
            ]);

            // If a leader is selected, add them as Project Manager
            if ($request->leader_id) {
                ProjectMember::create([
                    'project_id' => $project->id,
                    'user_id' => $request->leader_id, 
                    'role' => 'Project Manager',
                    'joined_at' => now(),
                ]);
            }

            // Create default boards for the project
            $name = ['To Do', 'In Progress', 'Review', 'Done'];
            $description = ['Tasks to be done', 'Tasks currently being worked on', 'Tasks waiting for review', 'Completed tasks'];
            
            foreach ($name as $index => $boardName) {
                Board::create([
                    'project_id' => $project->id,
                    'board_name' => $boardName,
                    'description' => $description[$index] ?? 'Board description',
                    'position' => $index + 1,
                ]);
            }

            DB::commit();
            Log::info('Project created successfully', ['project_id' => $project->id, 'user_id' => $user->id]);
            
            return redirect()->route('projects.show', $project->slug)->with('success', 'Project created successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create project', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            
            return back()->with('error', 'Failed to create project. Please try again.');
        }
    }

    public function searchUsers(Request $request)
    {
        $search = $request->get('search', '');
        $projectId = $request->get('project_id');
        $role = $request->get('role', '');
        
        if (empty($search)) {
            return response()->json([]);
        }

        // Get users not already in the project
        $query = User::where(function($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        });
        
        // Filter by role if specified
        if (!empty($role)) {
            $query->where('role', $role);
        }
        
        // Exclude users already in the project (if project_id is provided)
        if (!empty($projectId)) {
            $query->whereNotIn('id', function($subQuery) use ($projectId) {
                $subQuery->select('user_id')
                      ->from('project_members')
                      ->where('project_id', $projectId);
            });
        }
        
        $users = $query->limit(10)
            ->get(['id', 'name', 'email', 'role', 'avatar']);

        // Add avatar URL and initials for each user
        $users = $users->map(function ($user) use ($projectId) {
            // Check if user is assigned to any active or on_hold project
            // Only consider user as "working" if they're in an active or on_hold project
            // Users in completed, cancelled, or expired projects are available
            $isWorking = ProjectMember::where('user_id', $user->id)
                ->when($projectId, function($query, $projectId) {
                    return $query->where('project_id', '!=', $projectId);
                })
                ->whereHas('project', function($query) {
                    $query->whereIn('status', ['active', 'on_hold']);
                })
                ->exists();
            
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'avatar_url' => $user->getAvatarUrl(),
                'initials' => $user->getInitials(),
                'is_working' => $isWorking,
                'status' => $isWorking ? 'working' : 'available'
            ];
        });

        return response()->json($users);
    }

    public function addMember(Request $request, $slug)
    {
        $user = Auth::user();
        $project = Project::where('slug', $slug)->firstOrFail();

        // Check if user has permission to add members
        if ($user->role !== 'admin' && $project->user_id != $user->id) {
            return response()->json(['error' => 'You do not have permission to add members.'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:Developer,Designer',
        ]);

        // Get the user to be added
        $userToAdd = User::findOrFail($request->user_id);
        
        // Check if the user has 'user' role (only users with 'user' role can be added as members)
        if ($userToAdd->role !== 'user') {
            return response()->json(['error' => 'Only users with "User" role can be added as project members.'], 422);
        }

        // Check if user is already working on another active or on_hold project
        // Users in completed, cancelled, or expired projects can be recruited
        $isWorking = ProjectMember::where('user_id', $request->user_id)
            ->where('project_id', '!=', $project->id)
            ->whereHas('project', function($query) {
                $query->whereIn('status', ['active', 'on_hold']);
            })
            ->exists();
            
        if ($isWorking) {
            return response()->json(['error' => 'This user is already assigned to another active project and cannot be recruited.'], 422);
        }

        // Check if user is already a member
        $existingMember = ProjectMember::where('project_id', $project->id)
            ->where('user_id', $request->user_id)
            ->first();

        if ($existingMember) {
            return response()->json(['error' => 'User is already a member of this project.'], 422);
        }

        DB::beginTransaction();
        
        try {
            ProjectMember::create([
                'project_id' => $project->id,
                'user_id' => $request->user_id,
                'role' => $request->role,
                'joined_at' => now(),
            ]);

            // Update user status to working
            User::where('id', $request->user_id)->update(['status' => 'working']);

            DB::commit();
            Log::info('Member added to project', ['project_id' => $project->id, 'user_id' => $request->user_id]);
            
            $addedUser = User::find($request->user_id);

            return response()->json([
                'success' => 'Member added successfully.',
                'user' => $addedUser
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add member', ['project_id' => $project->id, 'error' => $e->getMessage()]);
            
            return response()->json(['error' => 'Failed to add member. Please try again.'], 500);
        }
    }

    public function removeMember(Request $request, $slug)
    {
        $user = Auth::user();
        $project = Project::where('slug', $slug)->firstOrFail();

        // Check if user has permission to remove members
        if ($user->role !== 'admin' && $project->user_id != $user->id) {
            return response()->json(['error' => 'You do not have permission to remove members.'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $member = ProjectMember::where('project_id', $project->id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$member) {
            return response()->json(['error' => 'User is not a member of this project.'], 404);
        }

        DB::beginTransaction();
        
        try {
            $userId = $member->user_id;
            
            // Delete member
            $member->delete();
            
            // Check if user has other active projects
            $hasOtherProjects = ProjectMember::where('user_id', $userId)
                ->whereHas('project', function($q) {
                    $q->whereIn('status', ['active', 'on_hold']);
                })
                ->exists();
            
            // Update user status to 'free' if no other active projects
            if (!$hasOtherProjects) {
                User::where('id', $userId)->update(['status' => 'free']);
            }
            
            DB::commit();
            Log::info('Member removed from project', ['project_id' => $project->id, 'user_id' => $userId]);
            
            return response()->json(['success' => 'Member removed successfully.']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove member', ['project_id' => $project->id, 'error' => $e->getMessage()]);
            
            return response()->json(['error' => 'Failed to remove member. Please try again.'], 500);
        }
    }

    /**
     * Complete a project
     */
    public function completeProject(Project $project)
    {
        $user = Auth::user();
        
        // Check if user has permission to complete project
        if ($user->role !== 'admin' && $project->user_id != $user->id) {
            return back()->with('error', 'You do not have permission to complete this project.');
        }

        $result = ProjectStatusService::completeProject($project);

        return back()->with('success', $result['message']);
    }

    /**
     * Cancel a project
     */
    public function cancelProject(Project $project)
    {
        $user = Auth::user();
        
        // Check if user has permission to cancel project
        if ($user->role !== 'admin' && $project->user_id != $user->id) {
            return back()->with('error', 'You do not have permission to cancel this project.');
        }

        $result = ProjectStatusService::cancelProject($project);

        return back()->with('success', $result['message']);
    }

    /**
     * Reactivate a project
     */
    public function reactivateProject(Project $project)
    {
        $user = Auth::user();
        
        // Check if user has permission to reactivate project
        if ($user->role !== 'admin' && $project->user_id != $user->id) {
            return back()->with('error', 'You do not have permission to reactivate this project.');
        }

        $result = ProjectStatusService::reactivateProject($project);

        return back()->with('success', $result['message']);
    }

    /**
     * Update project status
     */
    public function updateStatus(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has permission to update project status
        if ($user->role !== 'admin' && $project->user_id != $user->id) {
            return back()->with('error', 'You do not have permission to update this project status.');
        }

        $request->validate([
            'status' => 'required|in:active,completed,cancelled,on_hold,expired'
        ]);

        $oldStatus = $project->status;
        $newStatus = $request->status;

        // Handle different status transitions
        if ($newStatus === 'completed') {
            $result = ProjectStatusService::completeProject($project);
        } elseif ($newStatus === 'cancelled') {
            $result = ProjectStatusService::cancelProject($project);
        } elseif ($newStatus === 'active' && in_array($oldStatus, ['expired', 'cancelled', 'on_hold'])) {
            $result = ProjectStatusService::reactivateProject($project);
        } else {
            // Simple status update without member status changes
            $project->update(['status' => $newStatus]);
            $result = ['success' => true, 'message' => 'Project status updated successfully!'];
        }

        return back()->with('success', $result['message']);
    }
}
