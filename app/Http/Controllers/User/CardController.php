<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Card;
use App\Models\CardAssignment;
use App\Models\Project;
use App\Models\TimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CardController extends Controller
{
    /**
     * Display user's assigned cards
     */
    public function myCard()
    {
        $user = Auth::user();
        
        // Get cards assigned to the current user
        $cards = Card::where('user_id', $user->id)
                    ->with(['board.project', 'subtasks', 'comments'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Group cards by project for better organization
        $cardsByProject = $cards->groupBy(function($card) {
            return $card->board->project->project_name;
        });

        return view('pages.user.card.my-card', compact('cards', 'cardsByProject'));
    }

    /**
     * Update card status to review
     */
    public function updateToReview(Card $card)
    {
        $user = Auth::user();
        
        // Check if user owns this card (use loose comparison for type safety)
        if ($card->user_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if card is currently in progress
        if ($card->status !== 'in_progress') {
            return response()->json(['message' => 'Card must be in progress to submit for review'], 400);
        }

        $oldStatus = $card->status;

        // Update card status to review
        $card->update(['status' => 'review']);

        // Move card to Review board
        $this->moveCardToBoard($card, 'Review');

        // Stop active time log for this card (if any)
        $activeTimeLog = TimeLog::where('card_id', $card->id)
            ->where('user_id', $user->id)
            ->whereNull('end_time')
            ->latest()
            ->first();
        
        if ($activeTimeLog) {
            $activeTimeLog->update([
                'end_time' => now(),
                'description' => 'Card submitted for review'
            ]);
            $activeTimeLog->calculateDuration();
        }

        // Check if assignment already exists for this card
        $existingAssignment = CardAssignment::where('card_id', $card->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingAssignment) {
            // Update existing assignment back to pending_confirmation (re-submit after reject)
            $existingAssignment->update([
                'assignment_status' => 'pending_confirmation'
            ]);
        } else {
            // Create new card assignment record when submitting for review (first time)
            CardAssignment::create([
                'card_id' => $card->id,
                'user_id' => $user->id,
                'assigned_at' => $card->created_at ?? now(),
                'started_at' => $card->started_at ?? now(),
                'assignment_status' => 'pending_confirmation'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Card submitted for review successfully!'
        ]);
    }

    /**
     * Start working on card (change status from todo to in_progress)
     */
    public function startCard(Card $card)
    {
        $user = Auth::user();
        
        // Check if user owns this card (use loose comparison for type safety)
        if ($card->user_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if card is currently todo
        if ($card->status !== 'todo') {
            return response()->json(['message' => 'Card must be in todo status to start'], 400);
        }

        // Update card status to in_progress (Observer will handle started_at)
        $card->update(['status' => 'in_progress']);

        // Move card to In Progress board
        $this->moveCardToBoard($card, 'In Progress');

        // Create time log for card (start timer)
        TimeLog::create([
            'card_id' => $card->id,
            'user_id' => $user->id,
            'start_time' => now(),
            'description' => 'Started working on card'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Work started successfully!'
        ]);
    }

    /**
     * Move card to appropriate board based on status
     */
    private function moveCardToBoard(Card $card, $boardName)
    {
        $project = $card->board->project;

        // Find the target board
        $targetBoard = Board::where('project_id', $project->id)
                           ->where('board_name', $boardName)
                           ->first();

        if ($targetBoard && $targetBoard->id !== $card->board_id) {
            $card->update(['board_id' => $targetBoard->id]);
        }
    }

    /**
     * Get card details
     */
    public function show(Card $card)
    {
        $user = Auth::user();
        
        // Check if user owns this card (use loose comparison for type safety)
        if ($card->user_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $card->load(['board.project', 'subtasks', 'comments.user']);

        return response()->json([
            'status' => 'success',
            'data' => ['card' => $card]
        ]);
    }

    /**
     * Add comment to user's card
     */
    public function addComment(Request $request, Card $card)
    {
        $user = Auth::user();
        
        // Check if user owns this card (use loose comparison for type safety)
        if ($card->user_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'comment_text' => 'required|string|max:1000'
        ]);

        $comment = $card->comments()->create([
            'user_id' => $user->id,
            'comment_text' => $request->comment_text,
            'subtask_id' => null
        ]);

        $comment->load('user');

        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully!',
            'data' => ['comment' => $comment]
        ]);
    }

    /**
     * Get comments for user's card
     */
    public function getComments(Card $card)
    {
        $user = Auth::user();
        
        // Check if user owns this card (use loose comparison for type safety)
        if ($card->user_id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comments = $card->comments()
                        ->with('user')
                        ->orderBy('created_at', 'asc')
                        ->get();

        return response()->json([
            'status' => 'success',
            'data' => ['comments' => $comments]
        ]);
    }

    /**
     * Calculate actual working hours for a card
     * Called when card status changes to 'done'
     */
    public function calculateCardActualHours(Card $card)
    {
        if ($card->status !== 'done') {
            return;
        }

        $actualHours = $this->calculateActualHours($card);
        $card->update(['actual_hours' => $actualHours]);

        return $actualHours;
    }

    /**
     * Calculate actual working hours for a card
     * Based on work hours (8 AM - 4 PM, Monday-Friday) minus time logs
     */
    private function calculateActualHours($card)
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
