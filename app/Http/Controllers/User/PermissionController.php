<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Card;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function index()
    {
        // Get user's assigned card (same check as subtask page)
        $userCard = Card::where('user_id', Auth::id())->first();
        
        $permissions = Permission::where('user_id', Auth::id())
            ->with(['project', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        $projects = Project::whereHas('members', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->get();

        return view('pages.user.permission.permission', compact('permissions', 'projects', 'userCard'));
    }

    public function store(Request $request)
    {
        // Check if user has an assigned card
        $userCard = Card::where('user_id', Auth::id())->first();
        
        if (!$userCard) {
            return response()->json([
                'success' => false,
                'message' => 'You need to have an assigned card to submit permission requests.'
            ], 403);
        }

        $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'permission_type' => 'required|in:sick_leave,personal_leave,vacation,emergency,other',
            'reason' => 'required|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        $permission = Permission::create($data);
        
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
            'message' => 'Permission request submitted successfully!',
            'data' => $permission->load(['project', 'approvedBy'])
        ]);
    }

    public function show(Permission $permission)
    {
        // Verify user owns this permission
        if ($permission->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to permission request.');
        }

        return response()->json([
            'success' => true,
            'data' => $permission->load(['project', 'approvedBy'])
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        // Verify user owns this permission and it's still pending
        if ($permission->user_id !== Auth::id() || $permission->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update this permission request.'
            ], 403);
        }

        $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'permission_type' => 'required|in:sick_leave,personal_leave,vacation,emergency,other',
            'reason' => 'required|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);

        $permission->update($request->all());
        
        // Recalculate duration hours manually
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
            'message' => 'Permission request updated successfully!',
            'data' => $permission->fresh(['project', 'approvedBy'])
        ]);
    }

    public function destroy(Permission $permission)
    {
        // Verify user owns this permission and it's still pending
        if ($permission->user_id !== Auth::id() || $permission->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this permission request.'
            ], 403);
        }

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission request deleted successfully!'
        ]);
    }
}
