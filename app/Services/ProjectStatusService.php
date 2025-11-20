<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProjectStatusService
{
    /**
     * Update project and user status based on deadline and manual completion
     */
    public static function updateProjectAndUserStatus()
    {
        Log::info('Starting project and user status update process');
        
        // 1. Handle expired projects (deadline passed)
        static::handleExpiredProjects();
        
        // 2. Handle completed projects
        static::handleCompletedProjects();
        
        // 3. Handle cancelled projects
        static::handleCancelledProjects();
        
        Log::info('Project and user status update process completed');
    }
    
    /**
     * Handle projects that have passed their deadline
     */
    private static function handleExpiredProjects()
    {
        $expiredProjects = Project::where('deadline', '<', Carbon::now())
                                 ->where('status', 'active')
                                 ->get();
        
        Log::info('Found ' . $expiredProjects->count() . ' expired projects');
        
        foreach ($expiredProjects as $project) {
            // Update project status to expired
            $project->update(['status' => 'expired']);
            
            // Free project members
            static::freeProjectMembers($project, 'Project expired due to deadline');
            
            Log::info("Project '{$project->project_name}' marked as expired and members freed");
        }
    }
    
    /**
     * Handle manually completed projects
     */
    private static function handleCompletedProjects()
    {
        $completedProjects = Project::where('status', 'completed')
                                   ->whereHas('members', function($query) {
                                       $query->whereHas('user', function($userQuery) {
                                           $userQuery->where('status', 'working');
                                       });
                                   })
                                   ->get();
        
        Log::info('Found ' . $completedProjects->count() . ' completed projects with working members');
        
        foreach ($completedProjects as $project) {
            static::freeProjectMembers($project, 'Project completed');
            Log::info("Project '{$project->project_name}' members freed due to completion");
        }
    }
    
    /**
     * Handle cancelled projects
     */
    private static function handleCancelledProjects()
    {
        $cancelledProjects = Project::where('status', 'cancelled')
                                   ->whereHas('members', function($query) {
                                       $query->whereHas('user', function($userQuery) {
                                           $userQuery->where('status', 'working');
                                       });
                                   })
                                   ->get();
        
        Log::info('Found ' . $cancelledProjects->count() . ' cancelled projects with working members');
        
        foreach ($cancelledProjects as $project) {
            static::freeProjectMembers($project, 'Project cancelled');
            Log::info("Project '{$project->project_name}' members freed due to cancellation");
        }
    }
    
    /**
     * Free project members only if they are not involved in other active projects
     */
    private static function freeProjectMembers(Project $project, $reason = '')
    {
        $memberIds = $project->members->pluck('user_id');
        
        foreach ($memberIds as $userId) {
            // Check if user is involved in other active projects
            $activeProjectCount = ProjectMember::where('user_id', $userId)
                                              ->where('project_id', '!=', $project->id)
                                              ->whereHas('project', function($query) {
                                                  $query->where('status', 'active');
                                              })
                                              ->count();
            
            // Only free the user if they are not in any other active project
            if ($activeProjectCount === 0) {
                User::where('id', $userId)->update(['status' => 'free']);
                
                $user = User::find($userId);
                Log::info("User '{$user->name}' status changed to 'free'. Reason: {$reason}");
            } else {
                $user = User::find($userId);
                Log::info("User '{$user->name}' remains 'working' - involved in {$activeProjectCount} other active project(s)");
            }
        }
    }
    
    /**
     * Submit project for review (called by leader)
     */
    public static function completeProject(Project $project)
    {
        $project->update(['status' => 'review']);
        
        return [
            'success' => true,
            'message' => 'Project submitted for review successfully! Waiting for admin approval.'
        ];
    }
    
    /**
     * Approve project and mark as completed (called by admin)
     */
    public static function approveProject(Project $project)
    {
        $project->update(['status' => 'completed']);
        static::freeProjectMembers($project, 'Project approved and completed');
        
        return [
            'success' => true,
            'message' => 'Project approved and completed successfully! Team members status updated.'
        ];
    }
    
    /**
     * Reject project and return to active (called by admin)
     */
    public static function rejectProject(Project $project, $rejectionNote)
    {
        $project->update([
            'status' => 'active',
            'rejection_note' => $rejectionNote,
            'rejected_at' => now()
        ]);
        
        return [
            'success' => true,
            'message' => 'Project rejected and returned to active status.'
        ];
    }
    
    /**
     * Cancel a project and free its members
     */
    public static function cancelProject(Project $project)
    {
        $project->update(['status' => 'cancelled']);
        static::freeProjectMembers($project, 'Project cancelled');
        
        return [
            'success' => true,
            'message' => 'Project cancelled and team members status updated!'
        ];
    }
    
    /**
     * Reactivate a project (for expired or cancelled projects)
     */
    public static function reactivateProject(Project $project)
    {
        $project->update(['status' => 'active']);
        
        // Set all project members to working status
        $memberIds = $project->members->pluck('user_id');
        User::whereIn('id', $memberIds)->update(['status' => 'working']);
        
        return [
            'success' => true,
            'message' => 'Project reactivated and team members set to working status!'
        ];
    }
    
    /**
     * Get project status statistics
     */
    public static function getProjectStatusStats()
    {
        return [
            'active' => Project::where('status', 'active')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'expired' => Project::where('status', 'expired')->count(),
            'cancelled' => Project::where('status', 'cancelled')->count(),
            'on_hold' => Project::where('status', 'on_hold')->count(),
        ];
    }
}
