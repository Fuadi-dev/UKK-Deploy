<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    
    protected $fillable = [
        'user_id',
        'project_id',
        'permission_type',
        'reason',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'duration_hours',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Mutators & Accessors
    public function getPermissionTypeDisplayAttribute()
    {
        return match($this->permission_type) {
            'sick_leave' => 'Sick Leave',
            'personal_leave' => 'Personal Leave',
            'vacation' => 'Vacation',
            'emergency' => 'Emergency',
            'other' => 'Other',
            default => $this->permission_type,
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">Pending</span>',
            'approved' => '<span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Approved</span>',
            'rejected' => '<span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">Rejected</span>',
            default => $this->status,
        };
    }

    // Methods
    public function calculateDurationHours()
    {
        $start = Carbon::parse($this->start_date)->startOfDay();
        $end = Carbon::parse($this->end_date)->startOfDay();
        
        $totalHours = 0;
        $current = $start->copy();
        
        // Work hours: 8 AM - 4 PM (8 hours per day)
        $workHoursPerDay = 8;
        
        while ($current->lte($end)) {
            // Skip weekends
            if (!$current->isWeekend()) {
                if ($current->isSameDay($start) && $current->isSameDay($end)) {
                    // Same day permission
                    if ($this->start_time && $this->end_time) {
                        // Calculate hours between start_time and end_time
                        $startTimeStr = $this->start_time;
                        $endTimeStr = $this->end_time;
                        
                        // Parse time directly as H:i format
                        try {
                            $startTime = Carbon::createFromFormat('H:i', $startTimeStr);
                            $endTime = Carbon::createFromFormat('H:i', $endTimeStr);
                            
                            if ($startTime && $endTime && $startTime->lt($endTime)) {
                                $totalHours += $endTime->diffInHours($startTime);
                            }
                        } catch (\Exception $e) {
                            // If time parsing fails, use full day
                            $totalHours += $workHoursPerDay;
                        }
                    } else {
                        $totalHours += $workHoursPerDay;
                    }
                } elseif ($current->isSameDay($start)) {
                    // First day
                    if ($this->start_time) {
                        try {
                            $startTime = Carbon::createFromFormat('H:i', $this->start_time);
                            $endOfDay = Carbon::createFromFormat('H:i', '16:00');
                            if ($startTime && $startTime->lt($endOfDay)) {
                                $totalHours += $endOfDay->diffInHours($startTime);
                            }
                        } catch (\Exception $e) {
                            $totalHours += $workHoursPerDay;
                        }
                    } else {
                        $totalHours += $workHoursPerDay;
                    }
                } elseif ($current->isSameDay($end)) {
                    // Last day
                    if ($this->end_time) {
                        try {
                            $startOfDay = Carbon::createFromFormat('H:i', '08:00');
                            $endTime = Carbon::createFromFormat('H:i', $this->end_time);
                            if ($endTime && $endTime->gt($startOfDay)) {
                                $totalHours += $endTime->diffInHours($startOfDay);
                            }
                        } catch (\Exception $e) {
                            $totalHours += $workHoursPerDay;
                        }
                    } else {
                        $totalHours += $workHoursPerDay;
                    }
                } else {
                    // Full day
                    $totalHours += $workHoursPerDay;
                }
            }
            $current->addDay();
        }
        
        return $totalHours;
    }

    public function approve($adminId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    public function reject($adminId, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $adminId,
            'approved_at' => now(),
            'admin_notes' => $notes,
        ]);
    }
}
