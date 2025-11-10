<?php

namespace App\Observers;

use App\Models\TimeLog;

class TimeLogObserver
{
    /**
     * Handle the TimeLog "created" event.
     */
    public function created(TimeLog $timeLog): void
    {
        // Update actual hours when time log is created (though usually end_time is null)
        if ($timeLog->end_time) {
            $timeLog->updateActualHours();
        }
    }

    /**
     * Handle the TimeLog "updated" event.
     */
    public function updated(TimeLog $timeLog): void
    {
        // Update actual hours when end_time is set (work completed)
        if ($timeLog->end_time && $timeLog->wasChanged('end_time')) {
            $timeLog->updateActualHours();
        }
    }

    /**
     * Handle the TimeLog "deleted" event.
     */
    public function deleted(TimeLog $timeLog): void
    {
        // Recalculate actual hours when time log is deleted
        if ($timeLog->card_id) {
            $totalMinutes = TimeLog::where('card_id', $timeLog->card_id)
                ->whereNotNull('end_time')
                ->sum('duration_minutes');
            
            $actualHours = round($totalMinutes / 60, 2);
            \App\Models\Card::find($timeLog->card_id)?->update(['actual_hours' => $actualHours]);
            
        } elseif ($timeLog->subtask_id) {
            $totalMinutes = TimeLog::where('subtask_id', $timeLog->subtask_id)
                ->whereNotNull('end_time')
                ->sum('duration_minutes');
            
            $actualHours = round($totalMinutes / 60, 2);
            \App\Models\Subtask::find($timeLog->subtask_id)?->update(['actual_hours' => $actualHours]);
        }
    }

    /**
     * Handle the TimeLog "restored" event.
     */
    public function restored(TimeLog $timeLog): void
    {
        // Recalculate when restored
        if ($timeLog->end_time) {
            $timeLog->updateActualHours();
        }
    }

    /**
     * Handle the TimeLog "force deleted" event.
     */
    public function forceDeleted(TimeLog $timeLog): void
    {
        // Same as deleted
        $this->deleted($timeLog);
    }
}
