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
        
        // Team members count (original - not filtered)
        $teamMembersCountOriginal = User::whereHas('projectMembers.project.members', function ($query) use ($user) {
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

        // Calculate fastest and slowest workers based on completed cards with time logs
        $projectIds = $myProjects->pluck('id')->toArray();
        
        // Get all team members from leader's projects
        $teamMemberIds = User::whereHas('projectMembers.project', function ($query) use ($projectIds) {
            $query->whereIn('projects.id', $projectIds);
        })->where('id', '!=', $user->id)
            ->pluck('id')
            ->toArray();

        $userPerformance = [];
        
        foreach ($teamMemberIds as $memberId) {
            // Get completed cards for this member in leader's projects
            $completedCards = Card::where('user_id', $memberId)
                ->where('status', 'done')
                ->whereHas('board.project', function ($q) use ($projectIds) {
                    $q->whereIn('projects.id', $projectIds);
                })
                ->get();
            
            if ($completedCards->count() > 0) {
                $totalTime = 0;
                $cardCount = 0;
                
                foreach ($completedCards as $card) {
                    // Get time logs for this card
                    $cardTimeLogs = TimeLog::whereNotNull('end_time')
                        ->where('user_id', $memberId)
                        ->whereHas('subtask', function ($q) use ($card) {
                            $q->where('card_id', $card->id);
                        })
                        ->sum('duration_minutes');
                    
                    if ($cardTimeLogs > 0) {
                        $totalTime += $cardTimeLogs;
                        $cardCount++;
                    }
                }
                
                // Only add if user has time logs
                if ($cardCount > 0 && $totalTime > 0) {
                    $member = User::find($memberId);
                    $avgTimePerCard = $totalTime / $cardCount;
                    $userPerformance[] = [
                        'user' => $member,
                        'avg_time' => $avgTimePerCard,
                        'completed_cards' => $cardCount,
                        'total_hours' => round($totalTime / 60, 1)
                    ];
                }
            }
        }

        // Sort by average time and get fastest and slowest
        $fastestWorker = null;
        $slowestWorker = null;
        
        if (count($userPerformance) >= 2) {
            usort($userPerformance, function($a, $b) {
                return $a['avg_time'] <=> $b['avg_time'];
            });
            
            $fastestWorker = $userPerformance[0];
            $slowestWorker = end($userPerformance);
        } elseif (count($userPerformance) === 1) {
            $fastestWorker = $userPerformance[0];
        }

        // Calculate Top Performing Users (comprehensive metrics)
        $topPerformers = [];
        
        foreach ($teamMemberIds as $memberId) {
            $member = User::find($memberId);
            if (!$member) continue;
            
            // Get completed cards count
            $completedCardsCount = Card::where('user_id', $memberId)
                ->where('status', 'done')
                ->whereHas('board.project', function ($q) use ($projectIds) {
                    $q->whereIn('projects.id', $projectIds);
                })
                ->count();
            
            if ($completedCardsCount === 0) continue;
            
            // Calculate total time logs
            $totalMinutes = TimeLog::whereNotNull('end_time')
                ->where('user_id', $memberId)
                ->whereHas('subtask.card.board.project', function ($q) use ($projectIds) {
                    $q->whereIn('projects.id', $projectIds);
                })
                ->sum('duration_minutes');
            
            // Calculate cards completed on time (before or on due date)
            $onTimeCards = Card::where('user_id', $memberId)
                ->where('status', 'done')
                ->whereNotNull('due_date')
                ->whereNotNull('updated_at')
                ->whereRaw('updated_at <= due_date')
                ->whereHas('board.project', function ($q) use ($projectIds) {
                    $q->whereIn('projects.id', $projectIds);
                })
                ->count();
            
            // Calculate cards within estimated hours
            $withinEstimateCards = 0;
            $cardsWithEstimate = Card::where('user_id', $memberId)
                ->where('status', 'done')
                ->where('estimated_hours', '>', 0)
                ->whereHas('board.project', function ($q) use ($projectIds) {
                    $q->whereIn('projects.id', $projectIds);
                })
                ->get();
            
            foreach ($cardsWithEstimate as $card) {
                if ($card->actual_hours <= $card->estimated_hours) {
                    $withinEstimateCards++;
                }
            }
            
            // Calculate average completion time per card (in hours)
            $avgCompletionTime = $completedCardsCount > 0 ? ($totalMinutes / 60) / $completedCardsCount : 0;
            
            // Calculate on-time delivery rate
            $totalCardsWithDeadline = Card::where('user_id', $memberId)
                ->where('status', 'done')
                ->whereNotNull('due_date')
                ->whereHas('board.project', function ($q) use ($projectIds) {
                    $q->whereIn('projects.id', $projectIds);
                })
                ->count();
            
            $onTimeRate = $totalCardsWithDeadline > 0 ? ($onTimeCards / $totalCardsWithDeadline) * 100 : 0;
            
            // Calculate estimation accuracy rate
            $estimateAccuracyRate = $cardsWithEstimate->count() > 0 ? ($withinEstimateCards / $cardsWithEstimate->count()) * 100 : 0;
            
            // Calculate overall performance score (0-100)
            // Weighted scoring: Completion count (30%), On-time rate (35%), Estimate accuracy (35%)
            $performanceScore = 
                (min($completedCardsCount / 10, 1) * 30) + // Max 30 points for completing 10+ cards
                ($onTimeRate * 0.35) + // 35 points max for on-time delivery
                ($estimateAccuracyRate * 0.35); // 35 points max for estimate accuracy
            
            $topPerformers[] = [
                'user' => $member,
                'completed_cards' => $completedCardsCount,
                'total_hours' => round($totalMinutes / 60, 1),
                'avg_time_per_card' => round($avgCompletionTime, 1),
                'on_time_cards' => $onTimeCards,
                'on_time_rate' => round($onTimeRate, 1),
                'within_estimate_cards' => $withinEstimateCards,
                'estimate_accuracy_rate' => round($estimateAccuracyRate, 1),
                'performance_score' => round($performanceScore, 1),
            ];
        }
        
        // Sort by performance score and get top 3
        usort($topPerformers, function($a, $b) {
            return $b['performance_score'] <=> $a['performance_score'];
        });
        
        $topPerformingUsers = array_slice($topPerformers, 0, 3);

        $myProjectsCount = $totalProjects;
        $activeProjectsCount = $activeProjects;
        $teamMembersCount = $teamMembersCountOriginal;

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
            'teamTimeLogs',
            'fastestWorker',
            'slowestWorker',
            'topPerformingUsers'
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

