<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Support\Facades\Auth;
use App\Services\ProjectStatusService;

class ProjectController extends Controller
{
    /**
     * Display the leader's assigned project (redirect to detail since 1 user = 1 project)
     */
    public function myProjects()
    {
        $user = Auth::user();
        
        // Get the first project where the current user is assigned as a member
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['user', 'members.user'])->first();

        // If user has a project, redirect directly to detail
        if ($project) {
            return redirect()->route('leader.projects.show', $project->slug);
        }

        // If no project assigned, show empty state
        return view('pages.leader.project.my-project', compact('project'));
    }

    /**
     * Show details of a specific project assigned to the leader
     */
    public function showProject($slug)
    {
        $user = Auth::user();
        
        // Get the project where the current user is assigned as a member
        $project = Project::where('slug', $slug)
            ->whereHas('members', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['user', 'members.user'])
            ->firstOrFail();

        return view('pages.leader.project.detail', compact('project'));
    }

    /**
     * Update project status (leader can only complete their assigned projects)
     */
    public function updateStatus(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Verify leader is assigned to this project
        $isMember = ProjectMember::where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->exists();
            
        if (!$isMember) {
            return back()->with('error', 'You do not have permission to update this project.');
        }
        
        $request->validate([
            'status' => 'required|in:completed',
        ]);
        
        // Only allow completing projects
        if ($request->status === 'completed') {
            $result = ProjectStatusService::completeProject($project);
            return back()->with('success', $result['message']);
        }
        
        return back()->with('error', 'Invalid status update.');
    }
}
