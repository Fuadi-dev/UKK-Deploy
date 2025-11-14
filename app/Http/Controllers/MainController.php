<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Card;
use App\Models\Subtask;
use App\Models\User;
use App\Models\Permission;
use App\Models\TimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MainController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $data = [];

        switch ($user->role) {
            case 'admin':
                $data = $this->getAdminDashboardData();
                break;
            case 'leader':
                $data = $this->getLeaderDashboardData($user);
                break;
            case 'user':
            case 'designer':
            case 'developer':
                $data = $this->getUserDashboardData($user);
                break;
        }

        return view('dashboard', $data);
    }

    private function getAdminDashboardData()
    {
        // Admin Statistics
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $totalUsers = User::where('role', '!=', 'admin')->count();
        
        // User breakdown by role
        $leaders = User::where('role', 'leader')->count();
        $developers = User::where('role', 'developer')->count();
        $designers = User::where('role', 'designer')->count();
        
        // Project status breakdown
        $onHoldProjects = Project::where('status', 'on_hold')->count();
        $cancelledProjects = Project::where('status', 'cancelled')->count();
        $expiredProjects = Project::where('status', 'expired')->count();
        
        // Recent projects
        $recentProjects = Project::with(['members.user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Projects expiring soon (within 7 days)
        $expiringProjects = Project::where('status', 'active')
            ->where('deadline', '<=', Carbon::now()->addDays(7))
            ->where('deadline', '>=', Carbon::now())
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();
        
        // Pending permissions
        $pendingPermissions = Permission::where('status', 'pending')
            ->with(['user', 'project'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Available users (not assigned to any project)
        $availableUsers = User::whereDoesntHave('projectMembers')
            ->where('role', '!=', 'admin')
            ->count();
        
        // Busy users (assigned to projects)
        $busyUsers = User::whereHas('projectMembers')
            ->where('role', '!=', 'admin')
            ->count();

        $leadersCount = $leaders;
        $developersCount = $developers;
        $designersCount = $designers;

        return compact(
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'totalUsers',
            'leadersCount',
            'developersCount',
            'designersCount',
            'onHoldProjects',
            'cancelledProjects',
            'expiredProjects',
            'recentProjects',
            'expiringProjects',
            'availableUsers',
            'busyUsers'
        );
    }

    private function getLeaderDashboardData($user)
    {
        // Leader's projects
        $myProjects = Project::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->get();

        $totalProjects = $myProjects->count();
        $activeProjects = $myProjects->where('status', 'active')->count();
        $completedProjects = $myProjects->where('status', 'completed')->count();
        
        // Team members count
        $teamMembers = User::whereHas('projectMembers.project.members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->where('id', '!=', $user->id)->count();
        
        // Cards statistics
        $totalCards = Card::whereHas('board.project.members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->count();
        
        $todoCards = Card::whereHas('board.project.members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->where('status', 'todo')->count();
        
        $inProgressCards = Card::whereHas('board.project.members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->where('status', 'in_progress')->count();
        
        $reviewCards = Card::whereHas('board.project.members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->where('status', 'review')->count();
        
        $doneCards = Card::whereHas('board.project.members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->where('status', 'done')->count();
        
        // Recent projects
        $recentProjects = Project::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->with(['members.user'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
        
        // Cards needing review
        $cardsNeedingReview = Card::whereHas('board.project.members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->where('status', 'review')
            ->with(['assignments.user', 'board.project', 'user'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
        
        // Team permissions (pending)
        $teamPermissions = Permission::whereHas('user.projectMembers.project.members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->where('status', 'pending')
            ->with(['user', 'project'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Team time logs (recent)
        $teamTimeLogs = TimeLog::whereNotNull('end_time')
            ->whereHas('subtask.card.board.project.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('role', 'Project Manager');
            })
            ->with(['user', 'subtask.card'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $myProjectsCount = $totalProjects;
        $activeProjectsCount = $activeProjects;
        $teamMembersCount = $teamMembers;

        return compact(
            'myProjectsCount',
            'activeProjectsCount',
            'teamMembersCount',
            'todoCards',
            'inProgressCards',
            'reviewCards',
            'doneCards',
            'cardsNeedingReview',
            'teamPermissions',
            'teamTimeLogs'
        );
    }

    private function getUserDashboardData($user)
    {
        // User's current subtask (for developers/designers who work on subtasks)
        $currentSubtask = Subtask::where('user_id', $user->id)
            ->where('status', '!=', 'done')
            ->with(['card.board.project'])
            ->orderBy('updated_at', 'desc')
            ->first();
        
        // Get the card from current subtask
        $currentCard = $currentSubtask ? $currentSubtask->card : null;
        
        // Subtasks statistics (all user's subtasks in current card)
        $totalSubtasks = 0;
        $completedSubtasks = 0;
        $inProgressSubtasks = 0;
        $progressPercentage = 0;
        
        if ($currentCard) {
            // Get all subtasks for this user in the current card
            $mySubtasks = Subtask::where('card_id', $currentCard->id)
                ->where('user_id', $user->id)
                ->get();
            
            $totalSubtasks = $mySubtasks->count();
            $completedSubtasks = $mySubtasks->where('status', 'done')->count();
            $inProgressSubtasks = $mySubtasks->where('status', 'in_progress')->count();
            $progressPercentage = $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100, 2) : 0;
        }
        
        // Time logs statistics
        $todayHours = TimeLog::where('user_id', $user->id)
            ->whereNotNull('subtask_id')
            ->whereNotNull('end_time')
            ->whereDate('start_time', Carbon::today())
            ->sum('duration_minutes') / 60;
        
        $weekHours = TimeLog::where('user_id', $user->id)
            ->whereNotNull('subtask_id')
            ->whereNotNull('end_time')
            ->whereBetween('start_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('duration_minutes') / 60;
        
        $monthHours = TimeLog::where('user_id', $user->id)
            ->whereNotNull('subtask_id')
            ->whereNotNull('end_time')
            ->whereBetween('start_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('duration_minutes') / 60;
        
        // Check for running timer
        $runningTimer = TimeLog::where('user_id', $user->id)
            ->whereNotNull('subtask_id')
            ->whereNull('end_time')
            ->with('subtask')
            ->first();
        
        // Recent subtasks
        $recentSubtasks = Subtask::where('user_id', $user->id)
            ->with(['card.board.project'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
        
        // My permissions
        $myPermissions = Permission::where('user_id', $user->id)
            ->with(['project', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        $pendingPermissions = $myPermissions->where('status', 'pending')->count();
        $approvedPermissions = $myPermissions->where('status', 'approved')->count();
        
        // Project info from current card
        $projectName = $currentCard ? $currentCard->board->project->name : null;
        $cardTitle = $currentCard ? $currentCard->card_title : null;
        $cardStatus = $currentCard ? $currentCard->status : null;

        // Format hours for display
        $timeToday = round($todayHours, 1) . 'h';
        $timeThisWeek = round($weekHours, 1) . 'h';
        $timeThisMonth = round($monthHours, 1) . 'h';

        return compact(
            'currentCard',
            'totalSubtasks',
            'completedSubtasks',
            'inProgressSubtasks',
            'progressPercentage',
            'timeToday',
            'timeThisWeek',
            'timeThisMonth',
            'runningTimer',
            'recentSubtasks',
            'myPermissions',
            'pendingPermissions',
            'approvedPermissions',
            'projectName',
            'cardTitle',
            'cardStatus'
        );
    }
}

