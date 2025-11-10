<?php

namespace App\Observers;

use App\Models\Card;
use App\Models\TimeLog;
use Carbon\Carbon;

class CardObserver
{
    /**
     * Handle the Card "created" event.
     */
    public function created(Card $card): void
    {
        //
    }

    /**
     * Handle the Card "updated" event.
     */
    public function updated(Card $card): void
    {
        // Check if status changed to 'done'
        if ($card->isDirty('status') && $card->status === 'done') {
            $this->calculateAndUpdateActualHours($card);
        }
        
        // Track when card moves to 'in_progress' for time tracking
        if ($card->isDirty('status') && $card->status === 'in_progress' && !$card->started_at) {
            $card->updateQuietly(['started_at' => now()]);
        }
    }

    /**
     * Handle the Card "deleted" event.
     */
    public function deleted(Card $card): void
    {
        //
    }

    /**
     * Handle the Card "restored" event.
     */
    public function restored(Card $card): void
    {
        //
    }

    /**
     * Handle the Card "force deleted" event.
     */
    public function forceDeleted(Card $card): void
    {
        //
    }

    /**
     * Calculate and update actual working hours for a card
     */
    private function calculateAndUpdateActualHours(Card $card)
    {
        $actualHours = $this->calculateActualHours($card);
        
        // Update without triggering the observer again
        $card->updateQuietly(['actual_hours' => $actualHours]);
    }

    /**
     * Calculate actual working hours for a card
     * Based on work hours (8 AM - 4 PM, Monday-Friday) minus time logs
     */
    private function calculateActualHours(Card $card)
    {
        // Use started_at if available, otherwise fall back to created_at
        $startedAt = $card->started_at ? Carbon::parse($card->started_at) : Carbon::parse($card->created_at);
        $completedAt = Carbon::now(); // When status changed to 'done'

        // If card was never started (no started_at), return 0
        if (!$card->started_at) {
            return 0;
        }

        // Define work hours
        $workStartHour = 8;  // 8 AM
        $workEndHour = 16;   // 4 PM

        $totalWorkingHours = 0;
        $current = $startedAt->copy();

        while ($current->lt($completedAt)) {
            // Skip weekends
            if ($current->isWeekend()) {
                $current->addDay();
                continue;
            }

            // Calculate work hours for this day
            $dayStart = $current->copy()->setTime($workStartHour, 0, 0);
            $dayEnd = $current->copy()->setTime($workEndHour, 0, 0);

            // Adjust start time if card was started during work hours
            if ($current->isSameDay($startedAt) && $startedAt->hour >= $workStartHour && $startedAt->hour < $workEndHour) {
                $dayStart = $startedAt->copy();
            }

            // Adjust end time if card was completed during work hours
            if ($current->isSameDay($completedAt) && $completedAt->hour >= $workStartHour && $completedAt->hour < $workEndHour) {
                $dayEnd = $completedAt->copy();
            }

            // Only count if within work hours
            if ($dayStart->lt($dayEnd) && 
                $dayStart->hour < $workEndHour && 
                $dayEnd->hour >= $workStartHour) {
                
                // Ensure we're within work hours
                if ($dayStart->hour < $workStartHour) {
                    $dayStart->setTime($workStartHour, 0, 0);
                }
                if ($dayEnd->hour > $workEndHour) {
                    $dayEnd->setTime($workEndHour, 0, 0);
                }

                $hoursThisDay = $dayEnd->diffInHours($dayStart, true);
                $totalWorkingHours += $hoursThisDay;
            }

            $current->addDay();
        }

        // Subtract time from time logs (break time, etc.) for this card
        $timeLogHours = TimeLog::where('card_id', $card->id)
            ->where('user_id', $card->user_id)
            ->whereNotNull('duration_minutes')
            ->sum('duration_minutes') / 60; // Convert minutes to hours

        $actualHours = max(0, $totalWorkingHours - $timeLogHours);

        return round($actualHours, 2);
    }
}
