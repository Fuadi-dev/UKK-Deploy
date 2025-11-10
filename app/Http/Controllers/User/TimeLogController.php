<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TimeLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filter parameters
        $filter = $request->get('filter', 'all'); // all, today, week, month
        
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
        
        $timeLogs = $query->paginate(15);
        
        // Format time logs for view
        $timeLogs->getCollection()->transform(function ($log) {
            $log->task_name = $log->subtask ? $log->subtask->subtask_title : 'Unknown Subtask';
            $log->project_name = $log->subtask && $log->subtask->card && $log->subtask->card->board 
                ? $log->subtask->card->board->project->name 
                : 'Unknown Project';
            $log->duration = $log->getFormattedDurationAttribute();
            
            // Get estimated vs actual hours from subtask (actual_hours updated by Observer)
            if ($log->subtask) {
                $estimatedHours = $log->subtask->estimated_hours ?? 0;
                $actualHours = $log->subtask->actual_hours ?? 0; // Get from subtask table (updated by Observer)
                
                $log->estimated_hours = $estimatedHours;
                $log->actual_hours = $actualHours;
                $log->is_over_estimated = $actualHours > $estimatedHours && $estimatedHours > 0;
                $log->progress_percentage = $estimatedHours > 0 ? min(($actualHours / $estimatedHours) * 100, 150) : 0;
                $log->overtime_hours = $log->is_over_estimated ? round($actualHours - $estimatedHours, 2) : 0;
            }
            
            return $log;
        });
        
        // Get statistics
        $totalMinutes = TimeLog::where('user_id', $user->id)
            ->whereNotNull('subtask_id')
            ->whereNotNull('end_time')
            ->sum('duration_minutes');
        
        $weekMinutes = TimeLog::where('user_id', $user->id)
            ->whereNotNull('subtask_id')
            ->whereNotNull('end_time')
            ->thisWeek()
            ->sum('duration_minutes');
        
        $totalLogs = TimeLog::where('user_id', $user->id)
            ->whereNotNull('subtask_id')
            ->count();
        
        $averageMinutes = $totalLogs > 0 ? $totalMinutes / $totalLogs : 0;
        
        $totalTime = $this->formatMinutesToHours($totalMinutes);
        $weekTime = $this->formatMinutesToHours($weekMinutes);
        $averageTime = $this->formatMinutesToHours($averageMinutes);
        
        // Check for running timer
        $runningTimer = TimeLog::where('user_id', $user->id)
            ->whereNotNull('subtask_id')
            ->whereNull('end_time')
            ->first();
        
        if ($runningTimer && $runningTimer->subtask) {
            $runningTimer->task_name = $runningTimer->subtask->title;
            $runningTimer->duration = $this->calculateRunningDuration($runningTimer->start_time);
        }
        
        return view('pages.user.time-log.time-log', compact(
            'timeLogs',
            'totalTime',
            'weekTime',
            'totalLogs',
            'averageTime',
            'runningTimer',
            'filter'
        ));
    }

    public function show($id)
    {
        $timeLog = TimeLog::where('user_id', Auth::id())
            ->whereNotNull('subtask_id') // Only subtask time logs
            ->with(['subtask.card.board.project'])
            ->findOrFail($id);
        
        // Get estimated vs actual from subtask (actual_hours updated by Observer)
        $estimatedHours = $timeLog->subtask->estimated_hours ?? 0;
        $actualHours = $timeLog->subtask->actual_hours ?? 0;
        
        $data = [
            'success' => true,
            'time_log' => [
                'id' => $timeLog->id,
                'task_name' => $timeLog->subtask ? $timeLog->subtask->subtask_title : 'Unknown',
                'project_name' => $timeLog->subtask && $timeLog->subtask->card && $timeLog->subtask->card->board 
                    ? $timeLog->subtask->card->board->project->name 
                    : 'Unknown',
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
    
    private function calculateRunningDuration($startTime)
    {
        $start = Carbon::parse($startTime);
        $now = Carbon::now();
        $diffInMinutes = $now->diffInMinutes($start);
        
        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;
        $seconds = $now->diffInSeconds($start) % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
