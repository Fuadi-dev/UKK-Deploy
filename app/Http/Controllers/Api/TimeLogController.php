<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeLogController extends Controller
{
    /**
     * Get user's time logs (only subtasks)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filter parameters
        $filter = $request->get('filter', 'all'); // all, today, week, month
        $perPage = $request->get('per_page', 15);
        
        // Base query - ONLY SUBTASK TIME LOGS for users
        $query = TimeLog::with(['subtask.card.board.project'])
            ->where('user_id', $user->id)
            ->whereNotNull('subtask_id') // Only show subtask time logs
            ->orderBy('start_time', 'desc');
        
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
        
        $timeLogs = $query->paginate($perPage);
        
        // Format time logs for response
        $timeLogs->getCollection()->transform(function ($log) {
            return $this->formatTimeLog($log);
        });
        
        // Get statistics
        $stats = $this->getUserStats($user->id);
        
        // Check for running timer
        $runningTimer = TimeLog::where('user_id', $user->id)
            ->whereNotNull('subtask_id')
            ->whereNull('end_time')
            ->first();
        
        if ($runningTimer) {
            $runningTimer = $this->formatTimeLog($runningTimer);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'time_logs' => $timeLogs->items(),
                'pagination' => [
                    'total' => $timeLogs->total(),
                    'per_page' => $timeLogs->perPage(),
                    'current_page' => $timeLogs->currentPage(),
                    'last_page' => $timeLogs->lastPage(),
                    'from' => $timeLogs->firstItem(),
                    'to' => $timeLogs->lastItem(),
                ],
                'stats' => $stats,
                'running_timer' => $runningTimer,
            ]
        ]);
    }

    /**
     * Get single time log detail
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $timeLog = TimeLog::where('user_id', Auth::id())
            ->whereNotNull('subtask_id') // Only subtask time logs
            ->with(['subtask.card.board.project'])
            ->find($id);
        
        if (!$timeLog) {
            return response()->json([
                'success' => false,
                'message' => 'Time log not found or you do not have permission to view it.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $this->formatTimeLog($timeLog)
        ]);
    }

    /**
     * Get leader's team time logs (grouped by card)
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
                'message' => 'Unauthorized. Only leaders can access team time logs.'
            ], 403);
        }
        
        // Get filter parameters
        $filter = $request->get('filter', 'all');
        $member = $request->get('member', 'all');
        $perPage = $request->get('per_page', 15);
        
        // Get all time logs with filters
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
        
        // Get all time logs
        $allTimeLogs = $query->orderBy('start_time', 'desc')->get();
        
        // Group by card_id and aggregate data
        $groupedTimeLogs = $allTimeLogs->groupBy('card_id')->map(function ($logs) {
            return $this->aggregateCardTimeLogs($logs);
        });
        
        // Convert to collection and paginate manually
        $timeLogs = collect($groupedTimeLogs->values())->sortByDesc('start_time');
        
        $currentPage = $request->get('page', 1);
        $currentItems = $timeLogs->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $pagination = [
            'total' => $timeLogs->count(),
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'last_page' => ceil($timeLogs->count() / $perPage),
            'from' => (($currentPage - 1) * $perPage) + 1,
            'to' => min($currentPage * $perPage, $timeLogs->count()),
        ];
        
        // Get team members for filter
        $teamMembers = \App\Models\User::whereHas('projectMembers.project.members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('role', 'Project Manager');
        })->select('id', 'name', 'email', 'avatar')->get();
        
        // Get team stats
        $stats = $this->getTeamStats($user->id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'time_logs' => $currentItems,
                'pagination' => $pagination,
                'team_members' => $teamMembers,
                'stats' => $stats,
            ]
        ]);
    }

    /**
     * Get leader time log detail
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
                'message' => 'Unauthorized. Only leaders can access team time logs.'
            ], 403);
        }
        
        // Leader can view any card time log from their projects
        $timeLog = TimeLog::whereNotNull('card_id')
            ->whereHas('card.board.project.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('role', 'Project Manager');
            })
            ->with(['card.board.project', 'user'])
            ->find($id);
        
        if (!$timeLog) {
            return response()->json([
                'success' => false,
                'message' => 'Time log not found or you do not have permission to view it.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $this->formatLeaderTimeLog($timeLog)
        ]);
    }

    /**
     * Format time log for user response
     * 
     * @param TimeLog $log
     * @return array
     */
    private function formatTimeLog($log)
    {
        $estimatedHours = 0;
        $actualHours = 0;
        
        if ($log->subtask) {
            $estimatedHours = $log->subtask->estimated_hours ?? 0;
            $actualHours = $log->subtask->actual_hours ?? 0;
        }
        
        return [
            'id' => $log->id,
            'task_name' => $log->subtask ? $log->subtask->subtask_title : 'Unknown Subtask',
            'task_type' => 'subtask',
            'project_name' => $log->subtask && $log->subtask->card && $log->subtask->card->board 
                ? $log->subtask->card->board->project->name 
                : 'Unknown Project',
            'start_time' => $log->start_time,
            'end_time' => $log->end_time,
            'duration_minutes' => $log->duration_minutes,
            'duration_formatted' => $log->getFormattedDurationAttribute(),
            'description' => $log->description ?? 'Automatic tracking',
            'status' => $log->end_time ? 'completed' : 'running',
            'estimated_hours' => $estimatedHours,
            'actual_hours' => $actualHours,
            'is_over_estimated' => $actualHours > $estimatedHours && $estimatedHours > 0,
            'progress_percentage' => $estimatedHours > 0 ? min(($actualHours / $estimatedHours) * 100, 150) : 0,
            'overtime_hours' => ($actualHours > $estimatedHours && $estimatedHours > 0) ? round($actualHours - $estimatedHours, 2) : 0,
            'created_at' => $log->created_at,
            'updated_at' => $log->updated_at,
        ];
    }

    /**
     * Format time log for leader response
     * 
     * @param TimeLog $log
     * @return array
     */
    private function formatLeaderTimeLog($log)
    {
        $estimatedHours = $log->card->estimated_hours ?? 0;
        $actualHours = $log->card->actual_hours ?? 0;
        
        return [
            'id' => $log->id,
            'card_id' => $log->card_id,
            'task_name' => $log->card ? $log->card->card_title : 'Unknown Card',
            'task_type' => 'card',
            'project_name' => $log->card && $log->card->board && $log->card->board->project
                ? $log->card->board->project->name 
                : 'Unknown Project',
            'board_name' => $log->card && $log->card->board 
                ? $log->card->board->name 
                : 'Unknown Board',
            'worker_name' => $log->user ? $log->user->name : 'Unknown User',
            'worker_email' => $log->user ? $log->user->email : null,
            'start_time' => $log->start_time,
            'end_time' => $log->end_time,
            'duration_minutes' => $log->duration_minutes,
            'duration_formatted' => $log->getFormattedDurationAttribute(),
            'description' => $log->description ?? 'Automatic tracking',
            'status' => $log->end_time ? 'completed' : 'running',
            'estimated_hours' => $estimatedHours,
            'actual_hours' => $actualHours,
            'is_over_estimated' => $actualHours > $estimatedHours && $estimatedHours > 0,
            'progress_percentage' => $estimatedHours > 0 ? min(($actualHours / $estimatedHours) * 100, 150) : 0,
            'overtime_hours' => ($actualHours > $estimatedHours && $estimatedHours > 0) ? round($actualHours - $estimatedHours, 2) : 0,
            'created_at' => $log->created_at,
            'updated_at' => $log->updated_at,
        ];
    }

    /**
     * Aggregate card time logs for leader view
     * 
     * @param \Illuminate\Support\Collection $logs
     * @return array
     */
    private function aggregateCardTimeLogs($logs)
    {
        $latestLog = $logs->first();
        $totalDurationMinutes = $logs->whereNotNull('end_time')->sum('duration_minutes');
        $earliestStart = $logs->sortBy('start_time')->first()->start_time;
        $latestEnd = $logs->whereNotNull('end_time')->sortByDesc('end_time')->first()->end_time ?? null;
        $sessionCount = $logs->count();
        
        $estimatedHours = $latestLog->card->estimated_hours ?? 0;
        $actualHours = $latestLog->card->actual_hours ?? 0;
        
        $hours = floor($totalDurationMinutes / 60);
        $minutes = $totalDurationMinutes % 60;
        
        return [
            'id' => $latestLog->id,
            'card_id' => $latestLog->card_id,
            'task_name' => $latestLog->card ? $latestLog->card->card_title : 'Unknown Card',
            'task_type' => 'card',
            'project_name' => $latestLog->card && $latestLog->card->board && $latestLog->card->board->project
                ? $latestLog->card->board->project->name 
                : 'Unknown Project',
            'board_name' => $latestLog->card && $latestLog->card->board 
                ? $latestLog->card->board->name 
                : 'Unknown Board',
            'worker_name' => $latestLog->user ? $latestLog->user->name : 'Unknown User',
            'worker_email' => $latestLog->user ? $latestLog->user->email : null,
            'start_time' => $earliestStart,
            'end_time' => $latestEnd,
            'duration_minutes' => $totalDurationMinutes,
            'duration_formatted' => sprintf('%dh %dm', $hours, $minutes),
            'status' => $latestEnd ? 'completed' : 'running',
            'session_count' => $sessionCount,
            'estimated_hours' => $estimatedHours,
            'actual_hours' => $actualHours,
            'is_over_estimated' => $actualHours > $estimatedHours && $estimatedHours > 0,
            'progress_percentage' => $estimatedHours > 0 ? min(($actualHours / $estimatedHours) * 100, 150) : 0,
            'overtime_hours' => ($actualHours > $estimatedHours && $estimatedHours > 0) ? round($actualHours - $estimatedHours, 2) : 0,
        ];
    }

    /**
     * Get user statistics
     * 
     * @param int $userId
     * @return array
     */
    private function getUserStats($userId)
    {
        $totalMinutes = TimeLog::where('user_id', $userId)
            ->whereNotNull('subtask_id')
            ->whereNotNull('end_time')
            ->sum('duration_minutes');
        
        $todayMinutes = TimeLog::where('user_id', $userId)
            ->whereNotNull('subtask_id')
            ->whereNotNull('end_time')
            ->today()
            ->sum('duration_minutes');
        
        $weekMinutes = TimeLog::where('user_id', $userId)
            ->whereNotNull('subtask_id')
            ->whereNotNull('end_time')
            ->thisWeek()
            ->sum('duration_minutes');
        
        $monthMinutes = TimeLog::where('user_id', $userId)
            ->whereNotNull('subtask_id')
            ->whereNotNull('end_time')
            ->thisMonth()
            ->sum('duration_minutes');
        
        $totalLogs = TimeLog::where('user_id', $userId)
            ->whereNotNull('subtask_id')
            ->count();
        
        $averageMinutes = $totalLogs > 0 ? $totalMinutes / $totalLogs : 0;
        
        return [
            'total_hours' => round($totalMinutes / 60, 2),
            'today_hours' => round($todayMinutes / 60, 2),
            'week_hours' => round($weekMinutes / 60, 2),
            'month_hours' => round($monthMinutes / 60, 2),
            'average_hours' => round($averageMinutes / 60, 2),
            'total_logs' => $totalLogs,
            'total_formatted' => $this->formatMinutesToHours($totalMinutes),
            'today_formatted' => $this->formatMinutesToHours($todayMinutes),
            'week_formatted' => $this->formatMinutesToHours($weekMinutes),
            'month_formatted' => $this->formatMinutesToHours($monthMinutes),
            'average_formatted' => $this->formatMinutesToHours($averageMinutes),
        ];
    }

    /**
     * Get team statistics for leader
     * 
     * @param int $userId
     * @return array
     */
    private function getTeamStats($userId)
    {
        $totalMinutes = TimeLog::whereNotNull('card_id')
            ->whereNotNull('end_time')
            ->whereHas('card.board.project.members', function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('role', 'Project Manager');
            })
            ->sum('duration_minutes');
        
        $todayMinutes = TimeLog::whereNotNull('card_id')
            ->whereNotNull('end_time')
            ->whereHas('card.board.project.members', function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('role', 'Project Manager');
            })
            ->today()
            ->sum('duration_minutes');
        
        $weekMinutes = TimeLog::whereNotNull('card_id')
            ->whereNotNull('end_time')
            ->whereHas('card.board.project.members', function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('role', 'Project Manager');
            })
            ->thisWeek()
            ->sum('duration_minutes');
        
        $monthMinutes = TimeLog::whereNotNull('card_id')
            ->whereNotNull('end_time')
            ->whereHas('card.board.project.members', function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('role', 'Project Manager');
            })
            ->thisMonth()
            ->sum('duration_minutes');
        
        $totalLogs = TimeLog::whereNotNull('card_id')
            ->whereHas('card.board.project.members', function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('role', 'Project Manager');
            })
            ->count();
        
        $averageMinutes = $totalLogs > 0 ? $totalMinutes / $totalLogs : 0;
        
        return [
            'total_hours' => round($totalMinutes / 60, 2),
            'today_hours' => round($todayMinutes / 60, 2),
            'week_hours' => round($weekMinutes / 60, 2),
            'month_hours' => round($monthMinutes / 60, 2),
            'average_hours' => round($averageMinutes / 60, 2),
            'total_logs' => $totalLogs,
            'total_formatted' => $this->formatMinutesToHours($totalMinutes),
            'today_formatted' => $this->formatMinutesToHours($todayMinutes),
            'week_formatted' => $this->formatMinutesToHours($weekMinutes),
            'month_formatted' => $this->formatMinutesToHours($monthMinutes),
            'average_formatted' => $this->formatMinutesToHours($averageMinutes),
        ];
    }

    /**
     * Format minutes to hours string
     * 
     * @param float $minutes
     * @return string
     */
    private function formatMinutesToHours($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%dh %dm', $hours, $mins);
    }
}
