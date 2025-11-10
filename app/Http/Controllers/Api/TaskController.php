<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Subtask;
use App\Models\Comment;
use App\Models\TimeLog;
use App\Models\Board;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Get user's assigned card (Developer/Designer only gets 1 card)
     */
    public function getMyCard(Request $request)
    {
        $user = $request->user();
        
        // Get user's assigned card that is not done
        $card = Card::where('user_id', $user->id)
                   ->where('status', '!=', 'done')
                   ->with(['board.project', 'subtasks' => function($query) {
                       $query->orderBy('position')->orderBy('created_at');
                   }, 'comments.user'])
                   ->first();

        if (!$card) {
            return response()->json([
                'success' => true,
                'data' => [
                    'card' => null,
                    'message' => 'No active card assigned'
                ],
            ], 200);
        }

        // Add card statistics
        $cardData = $card->toArray();
        $cardData['statistics'] = [
            'total_subtasks' => $card->subtasks->count(),
            'completed_subtasks' => $card->subtasks->where('status', 'done')->count(),
            'in_progress_subtasks' => $card->subtasks->where('status', 'in_progress')->count(),
            'progress_percentage' => $card->subtasks->count() > 0 
                ? round(($card->subtasks->where('status', 'done')->count() / $card->subtasks->count()) * 100, 2) 
                : 0,
            'total_comments' => $card->comments->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => ['card' => $cardData],
        ], 200);
    }

    /**
     * Get card detail by ID
     */
    public function getCardDetail(Request $request, $cardId)
    {
        $user = $request->user();
        
        $card = Card::where('id', $cardId)
                   ->where('user_id', $user->id)
                   ->with(['board.project', 'subtasks' => function($query) {
                       $query->orderBy('position')->orderBy('created_at');
                   }, 'comments.user'])
                   ->first();

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Card not found or not assigned to you'
            ], 404);
        }

        $cardData = $card->toArray();
        $cardData['statistics'] = [
            'total_subtasks' => $card->subtasks->count(),
            'completed_subtasks' => $card->subtasks->where('status', 'done')->count(),
            'in_progress_subtasks' => $card->subtasks->where('status', 'in_progress')->count(),
            'progress_percentage' => $card->subtasks->count() > 0 
                ? round(($card->subtasks->where('status', 'done')->count() / $card->subtasks->count()) * 100, 2) 
                : 0,
            'total_comments' => $card->comments->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => ['card' => $cardData],
        ], 200);
    }

    /**
     * Start working on card (change status from todo to in_progress)
     */
    public function startCard(Request $request, $cardId)
    {
        $user = $request->user();
        
        $card = Card::where('id', $cardId)
                   ->where('user_id', $user->id)
                   ->first();

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Card not found or not assigned to you'
            ], 404);
        }

        if ($card->status !== 'todo') {
            return response()->json([
                'success' => false,
                'message' => 'Card must be in todo status to start'
            ], 400);
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
            'description' => 'Started working on card via API'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Work started successfully!',
            'data' => ['card' => $card->fresh()]
        ], 200);
    }

    /**
     * Update card status to review (Developer/Designer only)
     */
    public function updateCardStatus(Request $request, $cardId)
    {
        $user = $request->user();
        
        $request->validate([
            'status' => 'required|in:review,in_progress',
        ]);

        $card = Card::where('id', $cardId)
                   ->where('user_id', $user->id)
                   ->first();

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Card not found or not assigned to you'
            ], 404);
        }

        // If updating to review, check all subtasks are done
        if ($request->status === 'review') {
            if ($card->status !== 'in_progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'Card must be in progress to submit for review'
                ], 400);
            }

            $pendingSubtasks = $card->subtasks()->where('status', '!=', 'done')->count();
            if ($pendingSubtasks > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Complete all subtasks before submitting for review'
                ], 400);
            }

            // Stop active time log for this card (if any)
            $activeTimeLog = TimeLog::where('card_id', $card->id)
                ->where('user_id', $user->id)
                ->whereNull('end_time')
                ->latest()
                ->first();
            
            if ($activeTimeLog) {
                $activeTimeLog->update([
                    'end_time' => now(),
                    'description' => 'Card submitted for review via API'
                ]);
                $activeTimeLog->calculateDuration();
            }

            // Move card to Review board
            $this->moveCardToBoard($card, 'Review');
        }

        $card->status = $request->status;
        $card->save();

        return response()->json([
            'success' => true,
            'message' => 'Card status updated successfully!',
            'data' => ['card' => $card->fresh()],
        ], 200);
    }

    /**
     * Get card comments
     */
    public function getCardComments(Request $request, $cardId)
    {
        $user = $request->user();
        
        $card = Card::where('id', $cardId)
                   ->where('user_id', $user->id)
                   ->first();

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Card not found or not assigned to you'
            ], 404);
        }

        $comments = Comment::where('card_id', $card->id)
                          ->whereNull('subtask_id')
                          ->with('user:id,name,email,avatar')
                          ->orderBy('created_at', 'desc')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'comments' => $comments,
                'total' => $comments->count()
            ],
        ], 200);
    }

    /**
     * Add comment to card
     */
    public function addCardComment(Request $request, $cardId)
    {
        $user = $request->user();
        
        $card = Card::where('id', $cardId)
                   ->where('user_id', $user->id)
                   ->first();

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Card not found or not assigned to you'
            ], 404);
        }

        $request->validate([
            'comment_text' => 'required|string|max:1000'
        ]);

        $comment = Comment::create([
            'card_id' => $card->id,
            'user_id' => $user->id,
            'comment_text' => $request->comment_text,
            'comment_type' => 'card'
        ]);

        $comment->load('user:id,name,email,avatar');

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully!',
            'data' => ['comment' => $comment],
        ], 201);
    }

    /**
     * Get subtasks for user's card
     */
    public function getSubtasks(Request $request)
    {
        $user = $request->user();
        
        // Get user's assigned card
        $card = Card::where('user_id', $user->id)
                   ->where('status', '!=', 'done')
                   ->first();

        if (!$card) {
            return response()->json([
                'success' => true,
                'data' => [
                    'subtasks' => [],
                    'card' => null,
                    'message' => 'No active card assigned'
                ],
            ], 200);
        }

        $subtasks = Subtask::where('card_id', $card->id)
                          ->where('user_id', $user->id)
                          ->orderBy('position')
                          ->orderBy('created_at')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'subtasks' => $subtasks,
                'card' => [
                    'id' => $card->id,
                    'card_title' => $card->card_title,
                    'status' => $card->status,
                ],
            ],
        ], 200);
    }

    /**
     * Get single subtask detail
     */
    public function getSubtaskDetail(Request $request, $subtaskId)
    {
        $user = $request->user();
        
        $subtask = Subtask::where('id', $subtaskId)
                         ->where('user_id', $user->id)
                         ->with(['card.board.project'])
                         ->first();

        if (!$subtask) {
            return response()->json([
                'success' => false,
                'message' => 'Subtask not found or not owned by you'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['subtask' => $subtask],
        ], 200);
    }

    /**
     * Create subtask
     */
    public function createSubtask(Request $request)
    {
        $user = $request->user();
        
        // Get user's assigned card
        $card = Card::where('user_id', $user->id)
                   ->where('status', '!=', 'done')
                   ->first();

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'No active card assigned'
            ], 400);
        }

        $request->validate([
            'subtask_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        $subtask = Subtask::create([
            'card_id' => $card->id,
            'user_id' => $user->id,
            'subtask_title' => $request->subtask_title,
            'description' => $request->description,
            'status' => 'in_progress',
            'estimated_hours' => $request->estimated_hours ?? 0,
            'actual_hours' => 0,
            'position' => Subtask::where('card_id', $card->id)->count() + 1,
        ]);

        // Create time log for subtask (start timer automatically)
        TimeLog::create([
            'subtask_id' => $subtask->id,
            'user_id' => $user->id,
            'start_time' => now(),
            'description' => 'Subtask created and timer started via API'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subtask created successfully!',
            'data' => ['subtask' => $subtask],
        ], 201);
    }

    /**
     * Update subtask
     */
    public function updateSubtask(Request $request, $subtaskId)
    {
        $user = $request->user();
        
        $request->validate([
            'subtask_title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'estimated_hours' => 'sometimes|nullable|numeric|min:0',
        ]);

        $subtask = Subtask::where('id', $subtaskId)
                         ->where('user_id', $user->id)
                         ->first();

        if (!$subtask) {
            return response()->json([
                'success' => false,
                'message' => 'Subtask not found or not owned by you'
            ], 404);
        }

        $subtask->update($request->only([
            'subtask_title',
            'description',
            'estimated_hours',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Subtask updated successfully!',
            'data' => ['subtask' => $subtask->fresh()],
        ], 200);
    }

    /**
     * Toggle subtask completion status
     */
    public function toggleSubtaskStatus(Request $request, $subtaskId)
    {
        $user = $request->user();
        
        $subtask = Subtask::where('id', $subtaskId)
                         ->where('user_id', $user->id)
                         ->first();

        if (!$subtask) {
            return response()->json([
                'success' => false,
                'message' => 'Subtask not found or not owned by you'
            ], 404);
        }

        // Toggle between in_progress and done
        $newStatus = $subtask->status === 'done' ? 'in_progress' : 'done';
        
        // If marking as done, stop active time log
        if ($newStatus === 'done') {
            $activeTimeLog = TimeLog::where('subtask_id', $subtask->id)
                ->where('user_id', $user->id)
                ->whereNull('end_time')
                ->latest()
                ->first();
            
            if ($activeTimeLog) {
                $activeTimeLog->update([
                    'end_time' => now(),
                    'description' => 'Subtask completed via API'
                ]);
                $activeTimeLog->calculateDuration();
            }
        } 
        // If marking back to in_progress, start new time log
        else if ($newStatus === 'in_progress' && $subtask->status === 'done') {
            TimeLog::create([
                'subtask_id' => $subtask->id,
                'user_id' => $user->id,
                'start_time' => now(),
                'description' => 'Subtask resumed via API'
            ]);
        }
        
        $subtask->status = $newStatus;
        $subtask->save();

        return response()->json([
            'success' => true,
            'message' => 'Subtask status toggled successfully!',
            'data' => ['subtask' => $subtask->fresh()],
        ], 200);
    }

    /**
     * Get subtask comments
     */
    public function getSubtaskComments(Request $request, $subtaskId)
    {
        $user = $request->user();
        
        $subtask = Subtask::where('id', $subtaskId)
                         ->where('user_id', $user->id)
                         ->first();

        if (!$subtask) {
            return response()->json([
                'success' => false,
                'message' => 'Subtask not found or not owned by you'
            ], 404);
        }

        $comments = Comment::where('subtask_id', $subtask->id)
                          ->with('user:id,name,email,avatar')
                          ->orderBy('created_at', 'desc')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'comments' => $comments,
                'total' => $comments->count()
            ],
        ], 200);
    }

    /**
     * Add comment to subtask
     */
    public function addSubtaskComment(Request $request, $subtaskId)
    {
        $user = $request->user();
        
        $subtask = Subtask::where('id', $subtaskId)
                         ->where('user_id', $user->id)
                         ->first();

        if (!$subtask) {
            return response()->json([
                'success' => false,
                'message' => 'Subtask not found or not owned by you'
            ], 404);
        }

        $request->validate([
            'comment_text' => 'required|string|max:1000'
        ]);

        $comment = Comment::create([
            'subtask_id' => $subtask->id,
            'user_id' => $user->id,
            'comment_text' => $request->comment_text,
            'comment_type' => 'subtask'
        ]);

        $comment->load('user:id,name,email,avatar');

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully!',
            'data' => ['comment' => $comment],
        ], 201);
    }

    /**
     * Delete subtask
     */
    public function deleteSubtask(Request $request, $subtaskId)
    {
        $user = $request->user();
        
        $subtask = Subtask::where('id', $subtaskId)
                         ->where('user_id', $user->id)
                         ->first();

        if (!$subtask) {
            return response()->json([
                'success' => false,
                'message' => 'Subtask not found or not owned by you'
            ], 404);
        }

        // Stop active time log before deleting
        $activeTimeLog = TimeLog::where('subtask_id', $subtask->id)
            ->where('user_id', $user->id)
            ->whereNull('end_time')
            ->latest()
            ->first();
        
        if ($activeTimeLog) {
            $activeTimeLog->update([
                'end_time' => now(),
                'description' => 'Subtask deleted via API'
            ]);
            $activeTimeLog->calculateDuration();
        }

        $subtask->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subtask deleted successfully',
        ], 200);
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

    // Legacy methods for backward compatibility
    public function card(Request $request)
    {
        return $this->getMyCard($request);
    }

    public function updateCard(Request $request, $id)
    {
        return $this->updateCardStatus($request, $id);
    }

    public function subtask(Request $request)
    {
        return $this->getSubtasks($request);
    }

    public function postSubtask(Request $request)
    {
        return $this->createSubtask($request);
    }

    public function updateSubtaskStatus(Request $request, $id)
    {
        return $this->toggleSubtaskStatus($request, $id);
    }
}
