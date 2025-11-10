<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Card;
use App\Models\CardAssignment;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\TimeLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CardController extends Controller
{
    /**
     * Display cards management page for leader
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get the project where the current user is assigned as a member
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['boards.cards.assignedUser', 'boards.cards.subtasks', 'members.user'])->first();

        if (!$project) {
            return view('pages.leader.cards.index', ['project' => null, 'cards' => collect(), 'boards' => collect()]);
        }

        // Get all cards from project boards
        $cards = Card::whereIn('board_id', $project->boards->pluck('id'))
                    ->with(['assignedUser', 'board', 'subtasks'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        $boards = $project->boards;

        return view('pages.leader.cards.index', compact('project', 'cards', 'boards'));
    }

    /**
     * Create card
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Get the project where the current user is assigned
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->first();

        if (!$project) {
            return response()->json(['message' => 'No project assigned'], 404);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'card_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        // Get "In Progress" board for this project (cards always start here)
        $board = Board::where('project_id', $project->id)
                     ->where('board_name', 'To Do')
                     ->first();

        if (!$board) {
            return response()->json(['message' => 'In Progress board not found'], 404);
        }

        // Check if user is member of this project
        $isMember = ProjectMember::where('project_id', $project->id)
                                ->where('user_id', $request->user_id)
                                ->exists();

        if (!$isMember) {
            return response()->json(['message' => 'User is not a member of this project'], 400);
        }

        // Check if user already has an active card (for developer/designer)
        $assignedUser = User::find($request->user_id);
        if (in_array($assignedUser->role, ['user']) && $assignedUser->assignedCard) {
            return response()->json(['message' => 'User already has an active card'], 409);
        }

        $card = Card::create([
            'board_id' => $board->id,
            'user_id' => $request->user_id,
            'card_title' => $request->card_title,
            'slug' => Str::slug($request->card_title),
            'description' => $request->description,
            'position' => Card::where('board_id', $board->id)->count() + 1,
            'due_date' => $request->due_date,
            'status' => 'todo',
            'priority' => $request->priority,
            'estimated_hours' => $request->estimated_hours,
            'actual_hours' => 0,
        ]);

        // Update user status to working
        $assignedUser->status = 'working';
        $assignedUser->save();

        $card->load('assignedUser', 'subtasks', 'board');

        return response()->json([
            'status' => 'success',
            'message' => 'Card created successfully!',
            'data' => [
                'card' => $card,
            ],
        ], 201);
    }

    /**
     * Get project members for card assignment
     */
    public function getProjectMembers(Request $request)
    {
        $user = Auth::user();
        
        // Get the project where the current user is assigned
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->first();

        if (!$project) {
            return response()->json(['message' => 'No project assigned'], 404);
        }

        $search = $request->get('search', '');
        $membersQuery = ProjectMember::where('project_id', $project->id)
                                   ->with('user:id,name,email,role,status');

        // Apply search filter if provided
        if (!empty($search)) {
            $membersQuery->whereHas('user', function($query) use ($search) {
                $query->where('role', 'user')
                      ->where('status', 'free')
                      ->where(function($q) use ($search) {
                          $q->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('email', 'LIKE', '%' . $search . '%');
                      });
            });
        }

        $members = $membersQuery->get()
                               ->filter(function($member) use ($search) {
                                   // Additional filtering for non-search queries
                                   if (empty($search)) {
                                       return $member->user->role === 'user' && $member->user->status === 'free';
                                   }
                                   return $member->user && $member->user->role === 'user' && $member->user->status === 'free';
                               });

        return response()->json([
            'status' => 'success',
            'data' => [
                'members' => $members->values(),
            ],
        ], 200);
    }

    /**
     * Show single card with details
     */
    public function show(Card $card)
    {
        $user = Auth::user();
        
        // Check if user has access to this card
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereHas('boards', function($query) use ($card) {
            $query->where('id', $card->board_id);
        })->first();

        if (!$project) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $card->load(['assignedUser', 'board', 'subtasks', 'comments.user', 'timeLogs']);

        return response()->json([
            'status' => 'success',
            'data' => ['card' => $card]
        ]);
    }

    /**
     * Update card
     */
    public function update(Request $request, Card $card)
    {
        $user = Auth::user();
        
        // Check if user has access to this card
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereHas('boards', function($query) use ($card) {
            $query->where('id', $card->board_id);
        })->first();

        if (!$project) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'card_title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'estimated_hours' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:todo,in_progress,review,done',
        ]);

        $oldUserId = $card->user_id;
        $oldStatus = $card->status;

        // If changing assigned user, validate membership
        if ($request->has('user_id') && $request->user_id != $card->user_id) {
            $isMember = ProjectMember::where('project_id', $project->id)
                                    ->where('user_id', $request->user_id)
                                    ->exists();

            if (!$isMember) {
                return response()->json(['message' => 'User is not a member of this project'], 400);
            }

            $newUser = User::find($request->user_id);
            if ($newUser->role !== 'user' || $newUser->status !== 'free') {
                return response()->json(['message' => 'Selected user is not available'], 400);
            }

            // Update previous user status to free
            if ($oldUserId) {
                $oldUser = User::find($oldUserId);
                $oldUser->status = 'free';
                $oldUser->save();
            }

            // Update new user status to working
            $newUser->status = 'working';
            $newUser->save();
        }

        // Update card
        $card->update($request->only([
            'user_id', 'card_title', 'description', 'due_date', 
            'priority', 'estimated_hours', 'status'
        ]));

        if ($request->has('card_title')) {
            $card->slug = Str::slug($request->card_title);
            $card->save();
        }

        // Update board status based on card status
        $this->updateBoardStatus($card, $oldStatus);

        // Update user status based on card status change
        if ($request->has('status') && $request->status !== $oldStatus) {
            $this->updateUserStatusBasedOnCard($card, $request->status);
        }

        $card->load('assignedUser', 'board', 'subtasks');

        return response()->json([
            'status' => 'success',
            'message' => 'Card updated successfully!',
            'data' => ['card' => $card]
        ]);
    }

    /**
     * Update card board based on card status
     */
    private function updateBoardStatus(Card $card, $oldStatus)
    {
        $newStatus = $card->status;
        $project = $card->board->project;

        // Find the target board based on card status
        $targetBoardName = match($newStatus) {
            'in_progress' => 'In Progress',
            'review' => 'Review',
            'done' => 'Done',
            default => 'In Progress'
        };

        // Find the target board in the same project
        $targetBoard = Board::where('project_id', $project->id)
                           ->where('board_name', $targetBoardName)
                           ->first();

        if ($targetBoard && $targetBoard->id !== $card->board_id) {
            // Move card to the appropriate board
            $card->update(['board_id' => $targetBoard->id]);
        }
    }

    /**
     * Update user status based on card completion
     */
    private function updateUserStatusBasedOnCard(Card $card, $newStatus)
    {
        if ($card->user_id) {
            if ($newStatus === 'done') {
                // Check if user has other active cards
                $activeCards = Card::where('user_id', $card->user_id)
                                  ->where('id', '!=', $card->id)
                                  ->whereIn('status', ['in_progress', 'review'])
                                  ->count();
                
                if ($activeCards === 0) {
                    User::where('id', $card->user_id)->update(['status' => 'free']);
                }
            } else {
                // Ensure user is working for in_progress or review status
                User::where('id', $card->user_id)->update(['status' => 'working']);
            }
        }
    }

    /**
     * Delete card
     */
    public function destroy(Card $card)
    {
        $user = Auth::user();
        
        // Check if user has access to this card
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereHas('boards', function($query) use ($card) {
            $query->where('id', $card->board_id);
        })->first();

        if (!$project) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Update assigned user status to free
        $assignedUser = User::find($card->user_id);
        if ($assignedUser) {
            $assignedUser->status = 'free';
            $assignedUser->save();
        }

        // Delete card (cascade will handle related records)
        $card->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Card deleted successfully!'
        ]);
    }

    /**
     * Add comment to card
     */
    public function addComment(Request $request, Card $card)
    {
        $user = Auth::user();
        
        // Check if user has access to this card
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereHas('boards', function($query) use ($card) {
            $query->where('id', $card->board_id);
        })->first();

        if (!$project) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'comment_text' => 'required|string|max:1000'
        ]);

        $comment = $card->comments()->create([
            'user_id' => $user->id,
            'comment_text' => $request->comment_text,
            'comment_type' => 'card',
            'subtask_id' => null // Explicitly set subtask_id to null for card comments
        ]);

        $comment->load('user');

        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully!',
            'data' => ['comment' => $comment]
        ]);
    }

    /**
     * Get card comments
     */
    public function getComments(Card $card)
    {
        $user = Auth::user();
        
        // Check if user has access to this card
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereHas('boards', function($query) use ($card) {
            $query->where('id', $card->board_id);
        })->first();

        if (!$project) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comments = $card->comments()
                        ->with('user')
                        ->where('comment_type', 'card')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return response()->json([
            'status' => 'success',
            'data' => ['comments' => $comments]
        ]);
    }

    /**
     * Display assigned cards with review status for leader approval
     */
    public function assignedCards()
    {
        $user = Auth::user();
        
        // Get the project where the current user is assigned as a member (leader)
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['boards'])->first();

        if (!$project) {
            return view('pages.leader.assigned.index', [
                'project' => null, 
                'reviewCards' => collect()
            ]);
        }

        // Get all cards with status 'review' from project boards
        $reviewCards = Card::whereIn('board_id', $project->boards->pluck('id'))
                          ->where('status', 'review')
                          ->with(['assignedUser', 'board', 'subtasks'])
                          ->orderBy('updated_at', 'desc')
                          ->get();

        return view('pages.leader.assigned.index', compact('project', 'reviewCards'));
    }

    /**
     * Approve card - change status to done
     */
    public function approveCard(Card $card)
    {
        $user = Auth::user();
        
        // Check if user has access to this card
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereHas('boards', function($query) use ($card) {
            $query->where('id', $card->board_id);
        })->first();

        if (!$project) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if card is in review status
        if ($card->status !== 'review') {
            return response()->json(['message' => 'Card is not in review status'], 400);
        }

        $oldStatus = $card->status;

        // Update card status to done
        $card->update(['status' => 'done']);

        // Update board status using the helper method
        $this->updateBoardStatus($card, $oldStatus);

        // Update user status using the helper method
        $this->updateUserStatusBasedOnCard($card, 'done');

        // Update card assignment status to 'assigned' (completed successfully)
        $assignment = CardAssignment::where('card_id', $card->id)
            ->where('user_id', $card->user_id)
            ->first();
        
        if ($assignment) {
            $assignment->update([
                'assignment_status' => 'assigned',
                'completed_at' => now()
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Card approved and marked as done!'
        ]);
    }

    /**
     * Reject card - change status back to in progress
     */
    public function rejectCard(Card $card)
    {
        $user = Auth::user();
        
        // Check if user has access to this card
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereHas('boards', function($query) use ($card) {
            $query->where('id', $card->board_id);
        })->first();

        if (!$project) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if card is in review status
        if ($card->status !== 'review') {
            return response()->json(['message' => 'Card is not in review status'], 400);
        }

        $oldStatus = $card->status;

        // Update card status back to in progress
        $card->update(['status' => 'in_progress']);

        // Update board status using the helper method
        $this->updateBoardStatus($card, $oldStatus);

        // Update user status using the helper method
        $this->updateUserStatusBasedOnCard($card, 'in_progress');

        // Update assignment status to 'in_progress' when rejected (not delete)
        // This keeps the assignment record but marks it as in progress
        $assignment = CardAssignment::where('card_id', $card->id)
            ->where('user_id', $card->user_id)
            ->where('assignment_status', 'pending_confirmation')
            ->first();
        
        if ($assignment) {
            $assignment->update([
                'assignment_status' => 'in_progress'
            ]);
        }

        // Start new time log for card (rejected, need rework)
        TimeLog::create([
            'card_id' => $card->id,
            'user_id' => $card->user_id,
            'start_time' => now(),
            'description' => 'Card rejected - rework needed'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Card rejected and sent back to in progress!'
        ]);
    }

    /**
     * Display assignment history page
     */
    public function assignmentHistory()
    {
        $user = Auth::user();
        
        // Get the project where the current user is assigned as a member (leader)
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['boards'])->first();

        if (!$project) {
            return view('pages.leader.assigned.history', [
                'project' => null, 
                'assignments' => collect()
            ]);
        }

        // Get all card assignments from project with related data
        $assignments = CardAssignment::whereHas('card', function($query) use ($project) {
            $query->whereIn('board_id', $project->boards->pluck('id'));
        })
        ->with([
            'card' => function($query) {
                $query->with(['board.project', 'subtasks']);
            }, 
            'user'
        ])
        ->whereNotNull('card_id')
        ->whereNotNull('user_id')
        ->orderBy('created_at', 'desc')
        ->get();

        // Filter out assignments with missing relationships
        $assignments = $assignments->filter(function($assignment) {
            return $assignment->card !== null && 
                   $assignment->user !== null && 
                   $assignment->card->board !== null &&
                   $assignment->card->board->project !== null;
        });

        return view('pages.leader.assigned.history', compact('project', 'assignments'));
    }
}
