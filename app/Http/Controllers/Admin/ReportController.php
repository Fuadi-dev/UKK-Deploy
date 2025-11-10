<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectMember;
use App\Services\ProjectStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the main reports page
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Access denied. Admin only.');
        }

        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Generate comprehensive reports data
        $data = $this->generateReportsData($dateFrom, $dateTo);

        return view('pages.admin.reports.reports', compact('data', 'dateFrom', 'dateTo'));
    }

    /**
     * Print reports
     */
    public function print(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Access denied. Admin only.');
        }

        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Generate reports data
        $data = $this->generateReportsData($dateFrom, $dateTo);

        return view('pages.admin.reports.print', compact('data', 'dateFrom', 'dateTo'));
    }

    /**
     * Generate comprehensive reports data
     */
    private function generateReportsData($dateFrom, $dateTo)
    {
        $fromDate = Carbon::parse($dateFrom)->startOfDay();
        $toDate = Carbon::parse($dateTo)->endOfDay();

        // Project Statistics
        $projectStats = $this->getProjectStatistics($fromDate, $toDate);
        
        // User Statistics
        $userStats = $this->getUserStatistics();
        
        // Project Status Distribution
        $statusDistribution = $this->getProjectStatusDistribution();
        
        // Monthly Project Creation Trend
        $monthlyTrend = $this->getMonthlyProjectTrend();
        
        // Top Performers
        $topPerformers = $this->getTopPerformers();
        
        // Overdue Projects
        $overdueProjects = $this->getOverdueProjects();
        
        // User Workload
        $userWorkload = $this->getUserWorkload();
        
        // Recent Activities
        $recentActivities = $this->getRecentActivities($fromDate, $toDate);

        return compact(
            'projectStats',
            'userStats', 
            'statusDistribution',
            'monthlyTrend',
            'topPerformers',
            'overdueProjects',
            'userWorkload',
            'recentActivities'
        );
    }

    /**
     * Get project statistics
     */
    private function getProjectStatistics($fromDate, $toDate)
    {
        return [
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'active')->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'cancelled_projects' => Project::where('status', 'cancelled')->count(),
            'expired_projects' => Project::where('status', 'expired')->count(),
            'on_hold_projects' => Project::where('status', 'on_hold')->count(),
            'projects_created_in_period' => Project::whereBetween('created_at', [$fromDate, $toDate])->count(),
            'projects_completed_in_period' => Project::where('status', 'completed')
                ->whereBetween('updated_at', [$fromDate, $toDate])->count(),
        ];
    }

    /**
     * Get user statistics
     */
    private function getUserStatistics()
    {
        return [
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'leader_users' => User::where('role', 'leader')->count(),
            'regular_users' => User::where('role', 'user')->count(),
            'working_users' => User::where('status', 'working')->count(),
            'free_users' => User::where('status', 'free')->count(),
        ];
    }

    /**
     * Get project status distribution
     */
    private function getProjectStatusDistribution()
    {
        return Project::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Get monthly project creation trend (last 6 months)
     */
    private function getMonthlyProjectTrend()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        
        return Project::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $sixMonthsAgo)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                $item->month_name = Carbon::createFromDate($item->year, $item->month, 1)->format('M Y');
                return $item;
            });
    }

    /**
     * Get top performing users (most projects completed)
     */
    private function getTopPerformers()
    {
        return User::withCount(['projectMemberships as completed_projects' => function ($query) {
                $query->whereHas('project', function ($q) {
                    $q->where('status', 'completed');
                });
            }])
            ->having('completed_projects', '>', 0)
            ->orderBy('completed_projects', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get overdue projects
     */
    private function getOverdueProjects()
    {
        return Project::with(['user', 'members.user'])
            ->where('status', 'active')
            ->where('deadline', '<', Carbon::now())
            ->orderBy('deadline', 'asc')
            ->get();
    }

    /**
     * Get user workload distribution
     */
    private function getUserWorkload()
    {
        return User::with(['projectMemberships.project'])
            ->where('role', 'user')
            ->get()
            ->map(function ($user) {
                $activeProjects = $user->projectMemberships->filter(function ($membership) {
                    return $membership->project->status === 'active';
                })->count();
                
                return [
                    'user' => $user,
                    'active_projects' => $activeProjects,
                    'workload_status' => $activeProjects === 0 ? 'free' : ($activeProjects > 2 ? 'overloaded' : 'normal')
                ];
            });
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($fromDate, $toDate)
    {
        $projectActivities = Project::with('user')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'project_created',
                    'description' => "Project '{$project->project_name}' was created by {$project->user->name}",
                    'date' => $project->created_at,
                    'user' => $project->user->name
                ];
            });

        $memberActivities = ProjectMember::with(['user', 'project'])
            ->whereBetween('joined_at', [$fromDate, $toDate])
            ->orderBy('joined_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($member) {
                return [
                    'type' => 'member_joined',
                    'description' => "{$member->user->name} joined project '{$member->project->project_name}' as {$member->role}",
                    'date' => $member->joined_at,
                    'user' => $member->user->name
                ];
            });

        return $projectActivities->concat($memberActivities)
            ->sortByDesc('date')
            ->take(30);
    }
}
