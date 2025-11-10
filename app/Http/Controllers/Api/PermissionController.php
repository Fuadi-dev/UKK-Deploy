<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Card;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Get user's permission requests
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filter parameters
        $status = $request->get('status', 'all'); // all, pending, approved, rejected
        $perPage = $request->get('per_page', 15);
        
        // Base query
        $query = Permission::where('user_id', $user->id)
            ->with(['project', 'approvedBy'])
            ->orderBy('created_at', 'desc');
        
        // Apply status filter
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $permissions = $query->paginate($perPage);
        
        // Format permissions for response
        $permissions->getCollection()->transform(function ($permission) {
            return $this->formatPermission($permission);
        });
        
        // Get user's projects
        $projects = Project::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->select('id', 'project_name', 'status')->get();
        
        // Get statistics
        $stats = $this->getUserPermissionStats($user->id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $permissions->items(),
                'pagination' => [
                    'total' => $permissions->total(),
                    'per_page' => $permissions->perPage(),
                    'current_page' => $permissions->currentPage(),
                    'last_page' => $permissions->lastPage(),
                    'from' => $permissions->firstItem(),
                    'to' => $permissions->lastItem(),
                ],
                'projects' => $projects,
                'stats' => $stats,
            ]
        ]);
    }

    /**
     * Get single permission detail
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $permission = Permission::where('user_id', Auth::id())
            ->with(['project', 'approvedBy', 'user'])
            ->find($id);
        
        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission request not found or you do not have permission to view it.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $this->formatPermission($permission)
        ]);
    }

    /**
     * Create new permission request
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has an assigned card
        $userCard = Card::where('user_id', $user->id)->first();
        
        if (!$userCard) {
            return response()->json([
                'success' => false,
                'message' => 'You need to have an assigned card to submit permission requests.'
            ], 403);
        }
        
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'permission_type' => [
                'required',
                Rule::in(['sick_leave', 'personal_leave', 'vacation', 'emergency', 'other'])
            ],
            'reason' => 'required|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);
        
        // Create permission
        $permission = Permission::create([
            'user_id' => $user->id,
            'project_id' => $validated['project_id'] ?? null,
            'permission_type' => $validated['permission_type'],
            'reason' => $validated['reason'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'status' => 'pending',
        ]);
        
        // Calculate duration hours
        $durationHours = $this->calculateDurationHours(
            $validated['start_date'],
            $validated['end_date'],
            $validated['start_time'] ?? null,
            $validated['end_time'] ?? null
        );
        
        $permission->update(['duration_hours' => $durationHours]);
        
        return response()->json([
            'success' => true,
            'message' => 'Permission request submitted successfully!',
            'data' => $this->formatPermission($permission->fresh(['project', 'approvedBy']))
        ], 201);
    }

    /**
     * Update permission request (only if pending)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::where('user_id', Auth::id())->find($id);
        
        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission request not found.'
            ], 404);
        }
        
        // Only allow update if status is pending
        if ($permission->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update this permission request. Only pending requests can be edited.'
            ], 403);
        }
        
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'permission_type' => [
                'required',
                Rule::in(['sick_leave', 'personal_leave', 'vacation', 'emergency', 'other'])
            ],
            'reason' => 'required|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);
        
        // Update permission
        $permission->update($validated);
        
        // Recalculate duration hours
        $durationHours = $this->calculateDurationHours(
            $validated['start_date'],
            $validated['end_date'],
            $validated['start_time'] ?? null,
            $validated['end_time'] ?? null
        );
        
        $permission->update(['duration_hours' => $durationHours]);
        
        return response()->json([
            'success' => true,
            'message' => 'Permission request updated successfully!',
            'data' => $this->formatPermission($permission->fresh(['project', 'approvedBy']))
        ]);
    }

    /**
     * Delete permission request (only if pending)
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $permission = Permission::where('user_id', Auth::id())->find($id);
        
        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission request not found.'
            ], 404);
        }
        
        // Only allow delete if status is pending
        if ($permission->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this permission request. Only pending requests can be deleted.'
            ], 403);
        }
        
        $permission->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Permission request deleted successfully!'
        ]);
    }

    /**
     * Get leader's team permission requests
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function leaderIndex(Request $request)
    {
        $user = Auth::user();
        
        // Verify user is a leader
        if ($user->role !== 'leader') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only leaders can access team permission requests.'
            ], 403);
        }
        
        // Get filter parameters
        $status = $request->get('status', 'all');
        $projectId = $request->get('project_id', 'all');
        $userId = $request->get('user_id', 'all');
        $perPage = $request->get('per_page', 15);
        
        // Get permissions from team members in leader's projects
        $query = Permission::with(['user', 'project', 'approvedBy'])
            ->whereHas('user.projectMembers.project.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
            })
            ->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        if ($projectId !== 'all') {
            $query->where('project_id', $projectId);
        }
        
        if ($userId !== 'all') {
            $query->where('user_id', $userId);
        }
        
        $permissions = $query->paginate($perPage);
        
        // Format permissions
        $permissions->getCollection()->transform(function ($permission) {
            return $this->formatLeaderPermission($permission);
        });
        
        // Get leader's projects for filter
        $projects = Project::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->select('id', 'project_name', 'status')->get();
        
        // Get team members for filter
        $teamMembers = \App\Models\User::whereHas('projectMembers.project.members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->select('id', 'name', 'email', 'avatar')->get();
        
        // Get team statistics
        $stats = $this->getTeamPermissionStats($user->id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $permissions->items(),
                'pagination' => [
                    'total' => $permissions->total(),
                    'per_page' => $permissions->perPage(),
                    'current_page' => $permissions->currentPage(),
                    'last_page' => $permissions->lastPage(),
                    'from' => $permissions->firstItem(),
                    'to' => $permissions->lastItem(),
                ],
                'projects' => $projects,
                'team_members' => $teamMembers,
                'stats' => $stats,
            ]
        ]);
    }

    /**
     * Get team permission detail (leader only)
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function leaderShow($id)
    {
        $user = Auth::user();
        
        // Verify user is a leader
        if ($user->role !== 'leader') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only leaders can access team permission requests.'
            ], 403);
        }
        
        // Get permission from team member
        $permission = Permission::whereHas('user.projectMembers.project.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
            })
            ->with(['user', 'project', 'approvedBy'])
            ->find($id);
        
        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission request not found or you do not have permission to view it.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $this->formatLeaderPermission($permission)
        ]);
    }

    /**
     * Format permission for user response
     * 
     * @param Permission $permission
     * @return array
     */
    private function formatPermission($permission)
    {
        return [
            'id' => $permission->id,
            'project_id' => $permission->project_id,
            'project_name' => $permission->project ? $permission->project->project_name : null,
            'permission_type' => $permission->permission_type,
            'permission_type_display' => $permission->permission_type_display,
            'reason' => $permission->reason,
            'start_date' => $permission->start_date->format('Y-m-d'),
            'end_date' => $permission->end_date->format('Y-m-d'),
            'start_time' => $permission->start_time,
            'end_time' => $permission->end_time,
            'duration_hours' => $permission->duration_hours,
            'duration_formatted' => $this->formatHours($permission->duration_hours),
            'status' => $permission->status,
            'status_display' => ucfirst($permission->status),
            'admin_notes' => $permission->admin_notes,
            'approved_by_id' => $permission->approved_by,
            'approved_by_name' => $permission->approvedBy ? $permission->approvedBy->name : null,
            'approved_at' => $permission->approved_at ? $permission->approved_at->format('Y-m-d H:i:s') : null,
            'created_at' => $permission->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $permission->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Format permission for leader response
     * 
     * @param Permission $permission
     * @return array
     */
    private function formatLeaderPermission($permission)
    {
        return [
            'id' => $permission->id,
            'user_id' => $permission->user_id,
            'user_name' => $permission->user ? $permission->user->name : 'Unknown',
            'user_email' => $permission->user ? $permission->user->email : null,
            'user_avatar' => $permission->user ? $permission->user->avatar : null,
            'project_id' => $permission->project_id,
            'project_name' => $permission->project ? $permission->project->project_name : null,
            'permission_type' => $permission->permission_type,
            'permission_type_display' => $permission->permission_type_display,
            'reason' => $permission->reason,
            'start_date' => $permission->start_date->format('Y-m-d'),
            'end_date' => $permission->end_date->format('Y-m-d'),
            'start_time' => $permission->start_time,
            'end_time' => $permission->end_time,
            'duration_hours' => $permission->duration_hours,
            'duration_formatted' => $this->formatHours($permission->duration_hours),
            'status' => $permission->status,
            'status_display' => ucfirst($permission->status),
            'admin_notes' => $permission->admin_notes,
            'approved_by_id' => $permission->approved_by,
            'approved_by_name' => $permission->approvedBy ? $permission->approvedBy->name : null,
            'approved_at' => $permission->approved_at ? $permission->approved_at->format('Y-m-d H:i:s') : null,
            'created_at' => $permission->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $permission->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Calculate duration hours between dates/times
     * 
     * @param string $startDate
     * @param string $endDate
     * @param string|null $startTime
     * @param string|null $endTime
     * @return float
     */
    private function calculateDurationHours($startDate, $endDate, $startTime = null, $endTime = null)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $durationHours = 0;
        
        if ($startTime && $endTime) {
            // Same day with specific times
            if ($start->isSameDay($end)) {
                $startTimeCarbon = Carbon::createFromFormat('H:i', $startTime);
                $endTimeCarbon = Carbon::createFromFormat('H:i', $endTime);
                $durationHours = $endTimeCarbon->diffInHours($startTimeCarbon, true);
            } else {
                // Multi-day with times - calculate full days + partial hours
                $current = $start->copy();
                while ($current->lte($end)) {
                    if (!$current->isWeekend()) {
                        if ($current->isSameDay($start)) {
                            // First day
                            $startTimeCarbon = Carbon::createFromFormat('H:i', $startTime);
                            $endOfDay = Carbon::createFromFormat('H:i', '16:00');
                            if ($startTimeCarbon->lt($endOfDay)) {
                                $durationHours += $endOfDay->diffInHours($startTimeCarbon, true);
                            }
                        } elseif ($current->isSameDay($end)) {
                            // Last day
                            $startOfDay = Carbon::createFromFormat('H:i', '08:00');
                            $endTimeCarbon = Carbon::createFromFormat('H:i', $endTime);
                            if ($endTimeCarbon->gt($startOfDay)) {
                                $durationHours += $endTimeCarbon->diffInHours($startOfDay, true);
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
            $current = $start->copy();
            while ($current->lte($end)) {
                if (!$current->isWeekend()) {
                    $durationHours += 8; // 8 work hours per day
                }
                $current->addDay();
            }
        }
        
        return round($durationHours, 2);
    }

    /**
     * Get user permission statistics
     * 
     * @param int $userId
     * @return array
     */
    private function getUserPermissionStats($userId)
    {
        $total = Permission::where('user_id', $userId)->count();
        $pending = Permission::where('user_id', $userId)->where('status', 'pending')->count();
        $approved = Permission::where('user_id', $userId)->where('status', 'approved')->count();
        $rejected = Permission::where('user_id', $userId)->where('status', 'rejected')->count();
        
        $totalHours = Permission::where('user_id', $userId)
            ->where('status', 'approved')
            ->sum('duration_hours');
        
        $thisMonthHours = Permission::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->whereMonth('start_date', now()->month)
            ->sum('duration_hours');
        
        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'total_hours' => round($totalHours, 2),
            'this_month_hours' => round($thisMonthHours, 2),
            'total_hours_formatted' => $this->formatHours($totalHours),
            'this_month_hours_formatted' => $this->formatHours($thisMonthHours),
        ];
    }

    /**
     * Get team permission statistics (leader)
     * 
     * @param int $leaderId
     * @return array
     */
    private function getTeamPermissionStats($leaderId)
    {
        $baseQuery = Permission::whereHas('user.projectMembers.project.members', function ($q) use ($leaderId) {
            $q->where('user_id', $leaderId)
              ->where('role', 'Project Manager');
        });
        
        $total = (clone $baseQuery)->count();
        $pending = (clone $baseQuery)->where('status', 'pending')->count();
        $approved = (clone $baseQuery)->where('status', 'approved')->count();
        $rejected = (clone $baseQuery)->where('status', 'rejected')->count();
        
        $totalHours = (clone $baseQuery)
            ->where('status', 'approved')
            ->sum('duration_hours');
        
        $thisMonthHours = (clone $baseQuery)
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->whereMonth('start_date', now()->month)
            ->sum('duration_hours');
        
        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'total_hours' => round($totalHours, 2),
            'this_month_hours' => round($thisMonthHours, 2),
            'total_hours_formatted' => $this->formatHours($totalHours),
            'this_month_hours_formatted' => $this->formatHours($thisMonthHours),
        ];
    }

    /**
     * Format hours to readable string
     * 
     * @param float $hours
     * @return string
     */
    private function formatHours($hours)
    {
        $h = floor($hours);
        $m = round(($hours - $h) * 60);
        return sprintf('%dh %dm', $h, $m);
    }
}
