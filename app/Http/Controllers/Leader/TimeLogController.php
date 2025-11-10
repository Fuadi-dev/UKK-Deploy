<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\TimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filter parameters
        $filter = $request->get('filter', 'all'); // all, today, week, month
        $view = $request->get('view', 'team'); // Always default to team for leader
        $member = $request->get('member', 'all'); // all, specific user
        
        // Get all time logs with filters applied
        $query = TimeLog::with(['card.board.project', 'user'])
            ->whereNotNull('card_id') // Only show card time logs
            ->whereHas('card.board.project.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
            });
        
        // Apply date filters
        switch ($filter) {
            case 'today':
                $query->today();
                break;
            case 'week':
                $query->thisWeek();
                break;
            case 'month':
                $query->thisMonth();
                break;
        }
        
        // Apply member filter
        if ($member !== 'all') {
            $query->where('user_id', $member);
        }
        
        // Get all time logs (not paginated yet)
        $allTimeLogs = $query->orderBy('start_time', 'desc')->get();
        
        // Group by card_id and aggregate data
        $groupedTimeLogs = $allTimeLogs->groupBy('card_id')->map(function ($logs) {
            // Get the latest time log for card info
            $latestLog = $logs->first();
            
            // Calculate total duration from all time logs for this card
            $totalDurationMinutes = $logs->whereNotNull('end_time')->sum('duration_minutes');
            
            // Get earliest start time and latest end time
            $earliestStart = $logs->sortBy('start_time')->first()->start_time;
            $latestEnd = $logs->whereNotNull('end_time')->sortByDesc('end_time')->first()->end_time ?? null;
            
            // Count how many time logs (sessions) for this card
            $sessionCount = $logs->count();
            
            // Create aggregated log object
            $aggregatedLog = new \stdClass();
            $aggregatedLog->id = $latestLog->id; // Use latest log ID for view details
            $aggregatedLog->card_id = $latestLog->card_id;
            $aggregatedLog->card = $latestLog->card;
            $aggregatedLog->user = $latestLog->user;
            $aggregatedLog->start_time = $earliestStart;
            $aggregatedLog->end_time = $latestEnd;
            $aggregatedLog->duration_minutes = $totalDurationMinutes;
            $aggregatedLog->status = $latestEnd ? 'completed' : 'running';
            $aggregatedLog->description = $latestLog->description;
            $aggregatedLog->session_count = $sessionCount; // Number of work sessions
            
            // Format data for view
            $aggregatedLog->task_name = $latestLog->card ? $latestLog->card->card_title : 'Unknown Card';
            $aggregatedLog->task_type = 'Card';
            $aggregatedLog->project_name = $latestLog->card && $latestLog->card->board && $latestLog->card->board->project
                ? $latestLog->card->board->project->name 
                : 'Unknown Project';
            $aggregatedLog->board_name = $latestLog->card && $latestLog->card->board 
                ? $latestLog->card->board->name 
                : 'Unknown Board';
            $aggregatedLog->worker_name = $latestLog->user ? $latestLog->user->name : 'Unknown User';
            
            // Format duration (total from all sessions)
            $hours = floor($totalDurationMinutes / 60);
            $minutes = $totalDurationMinutes % 60;
            $aggregatedLog->formatted_duration = sprintf('%dh %dm', $hours, $minutes);
            
            // Get estimated vs actual hours from card (actual_hours updated by Observer)
            if ($latestLog->card) {
                $estimatedHours = $latestLog->card->estimated_hours ?? 0;
                $actualHours = $latestLog->card->actual_hours ?? 0;
                
                $aggregatedLog->estimated_hours = $estimatedHours;
                $aggregatedLog->actual_hours = $actualHours;
                $aggregatedLog->is_over_estimated = $actualHours > $estimatedHours && $estimatedHours > 0;
                $aggregatedLog->progress_percentage = $estimatedHours > 0 ? min(($actualHours / $estimatedHours) * 100, 150) : 0;
                $aggregatedLog->overtime_hours = $aggregatedLog->is_over_estimated ? round($actualHours - $estimatedHours, 2) : 0;
            }
            
            return $aggregatedLog;
        });
        
        // Convert to collection and sort by latest activity
        $timeLogs = collect($groupedTimeLogs->values())->sortByDesc('start_time');
        
        // Manual pagination
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $currentItems = $timeLogs->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $timeLogs = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $timeLogs->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        // Get team members for dropdown (all users in leader's projects)
        $teamMembers = \App\Models\User::whereHas('projectMembers.project.members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->get();
        
        // Get statistics
        $totalMinutes = TimeLog::whereNotNull('card_id')
            ->whereNotNull('end_time')
            ->whereHas('card.board.project.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('role', 'Project Manager');
            })
            ->sum('duration_minutes');
        
        $weekMinutes = TimeLog::whereNotNull('card_id')
            ->whereNotNull('end_time')
            ->whereHas('card.board.project.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('role', 'Project Manager');
            })
            ->thisWeek()
            ->sum('duration_minutes');
        
        $totalLogs = TimeLog::whereNotNull('card_id')
            ->whereHas('card.board.project.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('role', 'Project Manager');
            })
            ->count();
        
        $averageMinutes = $totalLogs > 0 ? $totalMinutes / $totalLogs : 0;
        
        $totalTime = $this->formatMinutesToHours($totalMinutes);
        $weekTime = $this->formatMinutesToHours($weekMinutes);
        $averageTime = $this->formatMinutesToHours($averageMinutes);
        
        // Calculate today's minutes
        $todayMinutes = TimeLog::whereNotNull('card_id')
            ->whereNotNull('end_time')
            ->whereHas('card.board.project.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('role', 'Project Manager');
            })
            ->today()
            ->sum('duration_minutes');
        
        // Calculate this month's minutes
        $monthMinutes = TimeLog::whereNotNull('card_id')
            ->whereNotNull('end_time')
            ->whereHas('card.board.project.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('role', 'Project Manager');
            })
            ->thisMonth()
            ->sum('duration_minutes');
        
        // Build stats array for view
        $stats = [
            'today_hours' => $todayMinutes / 60,
            'week_hours' => $weekMinutes / 60,
            'month_hours' => $monthMinutes / 60,
            'total_logs' => $totalLogs,
        ];
        
        // Get cards and subtasks for modal (even though not used in view-only mode)
        $userCards = collect(); // Empty collection
        $userSubtasks = collect(); // Empty collection
        
        return view('pages.leader.time-log.time-log', compact(
            'timeLogs',
            'teamMembers',
            'totalTime',
            'weekTime',
            'totalLogs',
            'averageTime',
            'filter',
            'view',
            'member',
            'stats',
            'userCards',
            'userSubtasks'
        ));
    }

    public function show($id)
    {
        $user = Auth::user();
        
        // Leader can view any card time log from their projects
        $timeLog = TimeLog::whereNotNull('card_id')
            ->whereHas('card.board.project.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('role', 'Project Manager');
            })
            ->with(['card.board.project', 'user'])
            ->findOrFail($id);
        
        // Calculate estimated vs actual for the card
        $estimatedHours = $timeLog->card->estimated_hours ?? 0;
        $actualHours = $timeLog->card->actual_hours ?? 0; // Get from card table (updated by Observer)
        
        $data = [
            'success' => true,
            'time_log' => [
                'id' => $timeLog->id,
                'task_name' => $timeLog->card ? $timeLog->card->card_title : 'Unknown',
                'project_name' => $timeLog->card && $timeLog->card->board && $timeLog->card->board->project
                    ? $timeLog->card->board->project->name 
                    : 'Unknown',
                'board_name' => $timeLog->card && $timeLog->card->board 
                    ? $timeLog->card->board->name 
                    : 'Unknown',
                'worker_name' => $timeLog->user ? $timeLog->user->name : 'Unknown',
                'start_time' => $timeLog->start_time,
                'end_time' => $timeLog->end_time,
                'duration' => $timeLog->getFormattedDurationAttribute(),
                'description' => $timeLog->description ?? 'Automatic tracking - No description',
                'estimated_hours' => $estimatedHours,
                'actual_hours' => $actualHours,
                'is_over_estimated' => $actualHours > $estimatedHours && $estimatedHours > 0,
                'progress_percentage' => $estimatedHours > 0 ? min(($actualHours / $estimatedHours) * 100, 150) : 0,
                'overtime_hours' => ($actualHours > $estimatedHours && $estimatedHours > 0) ? round($actualHours - $estimatedHours, 2) : 0,
            ]
        ];

        return response()->json($data);
    }
    
    private function formatMinutesToHours($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%dh %dm', $hours, $mins);
    }
}
