<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function index()
    {
        // Get permissions from projects where current user is Project Manager
        $permissions = Permission::with(['user', 'project', 'approvedBy'])
            ->whereHas('project.members', function ($query) {
                $query->where('user_id', Auth::id())
                      ->where('role', 'Project Manager');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get projects where current user is Project Manager
        $projects = Project::whereHas('members', function ($query) {
                $query->where('user_id', Auth::id())
                      ->where('role', 'Project Manager');
            })
            ->get();

        return view('pages.leader.permission.permission', compact('permissions', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
            'permission_type' => 'required|in:sick_leave,personal_leave,vacation,emergency,other',
            'reason' => 'required|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);

        $permission = Permission::create($request->all());
        
        // Calculate duration hours manually
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $durationHours = 0;
        
        if ($request->start_time && $request->end_time) {
            // Same day with specific times
            if ($startDate->isSameDay($endDate)) {
                $startTime = Carbon::createFromFormat('H:i', $request->start_time);
                $endTime = Carbon::createFromFormat('H:i', $request->end_time);
                $durationHours = $endTime->diffInHours($startTime);
            } else {
                // Multi-day with times - calculate full days + partial hours
                $current = $startDate->copy();
                while ($current->lte($endDate)) {
                    if (!$current->isWeekend()) {
                        if ($current->isSameDay($startDate)) {
                            // First day
                            $startTime = Carbon::createFromFormat('H:i', $request->start_time);
                            $endOfDay = Carbon::createFromFormat('H:i', '16:00');
                            if ($startTime->lt($endOfDay)) {
                                $durationHours += $endOfDay->diffInHours($startTime);
                            }
                        } elseif ($current->isSameDay($endDate)) {
                            // Last day
                            $startOfDay = Carbon::createFromFormat('H:i', '08:00');
                            $endTime = Carbon::createFromFormat('H:i', $request->end_time);
                            if ($endTime->gt($startOfDay)) {
                                $durationHours += $endTime->diffInHours($startOfDay);
                            }
                        } else {
                            // Full day
                            $durationHours += 8;
                        }
                    }
                    $current->addDay();
                }
            }
        } else {
            // Full days only
            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                if (!$current->isWeekend()) {
                    $durationHours += 8; // 8 work hours per day
                }
                $current->addDay();
            }
        }
        
        $permission->update(['duration_hours' => $durationHours]);

        return response()->json([
            'success' => true,
            'message' => 'Permission request created successfully!',
            'data' => $permission->load(['user', 'project'])
        ]);
    }

    public function approve(Request $request, Permission $permission)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        // Verify leader has permission to approve this request
        // Leader can approve if they are Project Manager in the project where user is member
        $hasPermission = $permission->user->projectMembers()
            ->whereHas('project.members', function ($query) {
                $query->where('user_id', Auth::id())
                      ->where('role', 'Project Manager');
            })->exists() || 
            ($permission->project && $permission->project->members()
                ->where('user_id', Auth::id())
                ->where('role', 'Project Manager')
                ->exists());

        if (!$hasPermission) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to approve this request.'
            ], 403);
        }

        $permission->approve(Auth::id(), $request->admin_notes);

        return response()->json([
            'success' => true,
            'message' => 'Permission request approved successfully!',
            'data' => $permission->fresh(['user', 'project', 'approvedBy'])
        ]);
    }

    public function reject(Request $request, Permission $permission)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        // Verify leader has permission to reject this request
        $hasPermission = $permission->user->projectMembers()
            ->whereHas('project.members', function ($query) {
                $query->where('user_id', Auth::id())
                      ->where('role', 'Project Manager');
            })->exists() || 
            ($permission->project && $permission->project->members()
                ->where('user_id', Auth::id())
                ->where('role', 'Project Manager')
                ->exists());

        if (!$hasPermission) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to reject this request.'
            ], 403);
        }

        $permission->reject(Auth::id(), $request->admin_notes);

        return response()->json([
            'success' => true,
            'message' => 'Permission request rejected successfully!',
            'data' => $permission->fresh(['user', 'project', 'approvedBy'])
        ]);
    }

    public function destroy(Permission $permission)
    {
        // Verify leader has permission to delete this request
        $hasPermission = $permission->user->projectMembers()
            ->whereHas('project.members', function ($query) {
                $query->where('user_id', Auth::id())
                      ->where('role', 'Project Manager');
            })->exists() || 
            ($permission->project && $permission->project->members()
                ->where('user_id', Auth::id())
                ->where('role', 'Project Manager')
                ->exists());

        if (!$hasPermission) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this request.'
            ], 403);
        }

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission request deleted successfully!'
        ]);
    }
}
