<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TimeLog extends Model
{
    protected $fillable = [
        'card_id',
        'subtask_id',
        'user_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'description'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the time log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the card that owns the time log.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    /**
     * Get the subtask that owns the time log.
     */
    public function subtask(): BelongsTo
    {
        return $this->belongsTo(Subtask::class);
    }

    // Accessors
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_minutes) {
            return '0 minutes';
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }

    public function getStatusAttribute()
    {
        return $this->end_time ? 'completed' : 'running';
    }

    public function getTaskNameAttribute()
    {
        if ($this->card) {
            return $this->card->title;
        } elseif ($this->subtask) {
            return $this->subtask->title;
        }
        return 'Unknown Task';
    }

    public function getTaskTypeAttribute()
    {
        if ($this->card_id) {
            return 'Card';
        } elseif ($this->subtask_id) {
            return 'Subtask';
        }
        return 'Unknown';
    }

    // Helper methods
    
    /**
     * Calculate duration in working hours only (Mon-Fri, 08:00-16:00)
     * Excludes approved permissions/leaves
     * Returns duration in minutes
     */
    public function calculateWorkingDuration()
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        
        // Working hours constants
        $workStart = 8; // 08:00
        $workEnd = 16;  // 16:00
        $workHoursPerDay = $workEnd - $workStart; // 8 hours
        
        // Get approved permissions for this user during this period
        $approvedPermissions = Permission::where('user_id', $this->user_id)
            ->where('status', 'approved')
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start->toDateString(), $end->toDateString()])
                      ->orWhereBetween('end_date', [$start->toDateString(), $end->toDateString()])
                      ->orWhere(function($q) use ($start, $end) {
                          $q->where('start_date', '<=', $start->toDateString())
                            ->where('end_date', '>=', $end->toDateString());
                      });
            })
            ->get();
        
        $totalMinutes = 0;
        $current = $start->copy();
        
        while ($current->lt($end)) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($current->dayOfWeek === Carbon::SATURDAY || $current->dayOfWeek === Carbon::SUNDAY) {
                $current->addDay()->startOfDay();
                continue;
            }
            
            // Check if current date has approved permission
            $hasPermission = $this->hasPermissionOnDate($current, $approvedPermissions);
            
            if ($hasPermission) {
                // Skip this day - user has approved leave/permission
                $current->addDay()->startOfDay();
                continue;
            }
            
            // Get working hours for current day
            $dayStart = $current->copy()->setTime($workStart, 0, 0);
            $dayEnd = $current->copy()->setTime($workEnd, 0, 0);
            
            // Determine actual start time for this day
            if ($current->lt($dayStart)) {
                $actualStart = $dayStart;
            } else if ($current->gt($dayEnd)) {
                // Already past work hours, move to next day
                $current->addDay()->startOfDay();
                continue;
            } else {
                $actualStart = $current->copy();
            }
            
            // Determine actual end time for this day
            if ($end->gt($dayEnd) && $end->isSameDay($current)) {
                $actualEnd = $dayEnd;
            } else if ($end->isSameDay($current)) {
                $actualEnd = $end->lt($dayStart) ? $dayStart : ($end->gt($dayEnd) ? $dayEnd : $end);
            } else {
                $actualEnd = $dayEnd;
            }
            
            // Calculate minutes for this day (only if within working hours)
            if ($actualStart->lt($actualEnd)) {
                $totalMinutes += $actualStart->diffInMinutes($actualEnd);
            }
            
            // Move to next day
            $current->addDay()->startOfDay();
        }
        
        return $totalMinutes;
    }
    
    /**
     * Check if user has approved permission on specific date
     */
    private function hasPermissionOnDate($date, $permissions)
    {
        foreach ($permissions as $permission) {
            $permStart = Carbon::parse($permission->start_date)->startOfDay();
            $permEnd = Carbon::parse($permission->end_date)->endOfDay();
            
            if ($date->between($permStart, $permEnd)) {
                // Check if it's a partial day permission
                if ($permission->start_time && $permission->end_time && 
                    $permStart->isSameDay($permEnd)) {
                    // For partial day, we still count it as having permission
                    // You could make this more granular if needed
                    return true;
                }
                return true;
            }
        }
        
        return false;
    }
    
    public function calculateDuration()
    {
        if ($this->start_time && $this->end_time) {
            // Use working hours calculation
            $this->duration_minutes = $this->calculateWorkingDuration();
            $this->save();
        }
    }

    public function stop()
    {
        if (!$this->end_time) {
            $this->end_time = now();
            $this->calculateDuration();
            $this->updateActualHours(); // Update actual hours when stopping
        }
    }
    
    /**
     * Update actual_hours in Card or Subtask based on total time logs
     */
    public function updateActualHours()
    {
        if ($this->card_id) {
            // Sum all completed time logs for this card
            $totalMinutes = TimeLog::where('card_id', $this->card_id)
                ->whereNotNull('end_time')
                ->sum('duration_minutes');
            
            $actualHours = round($totalMinutes / 60, 2);
            
            // Update card's actual_hours
            $this->card()->update(['actual_hours' => $actualHours]);
            
        } elseif ($this->subtask_id) {
            // Sum all completed time logs for this subtask
            $totalMinutes = TimeLog::where('subtask_id', $this->subtask_id)
                ->whereNotNull('end_time')
                ->sum('duration_minutes');
            
            $actualHours = round($totalMinutes / 60, 2);
            
            // Update subtask's actual_hours
            $this->subtask()->update(['actual_hours' => $actualHours]);
        }
    }

    // Scopes
    public function scopeRunning($query)
    {
        return $query->whereNull('end_time');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('end_time');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_time', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('start_time', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('start_time', now()->month)
                    ->whereYear('start_time', now()->year);
    }
}
