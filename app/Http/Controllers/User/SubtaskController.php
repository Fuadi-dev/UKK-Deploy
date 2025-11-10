<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Subtask;
use App\Models\Card;
use App\Models\TimeLog;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubtaskController extends Controller
{
    public function index()
    {
        // Get user's card (assuming 1 user 1 card)
        $userCard = Card::where('user_id', Auth::id())->first();
        
        if (!$userCard) {
            return view('pages.user.subtask.subtask', [
                'subtasks' => collect([]),
                'userCard' => null
            ]);
        }

        // Get user's subtasks with relationships
        $subtasks = Subtask::with(['card.board.project', 'comments'])
            ->where('user_id', Auth::id())
            ->where('card_id', $userCard->id)
            ->orderBy('position')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.user.subtask.subtask', compact('subtasks', 'userCard'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subtask_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        // Get user's card (assuming 1 user 1 card)
        $userCard = Card::where('user_id', Auth::id())->first();
        
        if (!$userCard) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have an assigned card.'
            ], 404);
        }

        $subtask = Subtask::create([
            'card_id' => $userCard->id,
            'user_id' => Auth::id(),
            'subtask_title' => $request->subtask_title,
            'description' => $request->description,
            'estimated_hours' => $request->estimated_hours,
            'status' => 'in_progress',
        ]);

        // Create time log for subtask (start timer automatically)
        TimeLog::create([
            'subtask_id' => $subtask->id,
            'user_id' => Auth::id(),
            'start_time' => now(),
            'description' => 'Subtask created and timer started'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subtask created successfully!',
            'data' => [
                'subtask' => $subtask->load(['card.board.project'])
            ]
        ]);
    }

    public function show($id)
    {
        $subtask = Subtask::with(['card.board.project'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'subtask' => $subtask
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $subtask = Subtask::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'subtask_title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        $subtask->update($request->only([
            'subtask_title',
            'description', 
            'estimated_hours'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Subtask updated successfully!',
            'data' => [
                'subtask' => $subtask->load(['card.board.project'])
            ]
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $subtask = Subtask::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'status' => 'required|in:in_progress,done'
        ]);

        $updateData = [
            'status' => $request->status
        ];

        // Calculate actual hours and stop timer when marking as done
        if ($request->status === 'done' && $subtask->status !== 'done') {
            $actualHours = $this->calculateActualHours($subtask);
            $updateData['actual_hours'] = $actualHours;
            
            // Stop active time log for this subtask
            $activeTimeLog = TimeLog::where('subtask_id', $subtask->id)
                ->where('user_id', Auth::id())
                ->whereNull('end_time')
                ->latest()
                ->first();
            
            if ($activeTimeLog) {
                $activeTimeLog->update([
                    'end_time' => now(),
                    'description' => 'Subtask completed'
                ]);
                $activeTimeLog->calculateDuration();
            }
        }

        $subtask->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Subtask status updated successfully!',
            'data' => [
                'subtask' => $subtask
            ]
        ]);
    }

    /**
     * Calculate actual working hours for a subtask
     * Based on work hours (9 AM - 5 PM, Monday-Friday) minus time logs
     */
    private function calculateActualHours($subtask)
    {
        $createdAt = Carbon::parse($subtask->created_at);
        $completedAt = Carbon::now();

        // Define work hours
        $workStartHour = 8; // 8 AM
        $workEndHour = 16;  // 4 PM

        $totalWorkingHours = 0;
        $current = $createdAt->copy();

        while ($current->lt($completedAt)) {
            // Skip weekends
            if ($current->isWeekend()) {
                $current->addDay();
                continue;
            }

            // Calculate work hours for this day
            $dayStart = $current->copy()->setTime($workStartHour, 0, 0);
            $dayEnd = $current->copy()->setTime($workEndHour, 0, 0);

            // Adjust start time if subtask was created during work hours
            if ($current->isSameDay($createdAt) && $createdAt->hour >= $workStartHour && $createdAt->hour < $workEndHour) {
                $dayStart = $createdAt->copy();
            }

            // Adjust end time if subtask was completed during work hours
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

        // Subtract time from time logs (break time, etc.)
        $timeLogHours = TimeLog::where('subtask_id', $subtask->id)
            ->where('user_id', Auth::id())
            ->whereNotNull('duration_minutes')
            ->sum('duration_minutes') / 60; // Convert minutes to hours

        $actualHours = max(0, $totalWorkingHours - $timeLogHours);

        return round($actualHours, 2);
    }

    public function destroy($id)
    {
        $subtask = Subtask::where('user_id', Auth::id())->findOrFail($id);
        $subtask->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subtask deleted successfully!'
        ]);
    }

    /**
     * Get comments for a subtask
     */
    public function getComments($id)
    {
        $subtask = Subtask::where('user_id', Auth::id())->findOrFail($id);
        
        $comments = Comment::where('subtask_id', $subtask->id)
            ->with('user:id,name,avatar')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'comments' => $comments,
                'total' => $comments->count()
            ]
        ]);
    }

    /**
     * Add a comment to a subtask
     */
    public function addComment(Request $request, $id)
    {
        $subtask = Subtask::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'comment_text' => 'required|string|max:1000'
        ]);

        $comment = Comment::create([
            'subtask_id' => $subtask->id,
            'user_id' => Auth::id(),
            'comment_text' => $request->comment_text,
            'comment_type' => 'subtask'
        ]);

        $comment->load('user:id,name,avatar');

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully!',
            'data' => [
                'comment' => $comment
            ]
        ]);
    }
}
