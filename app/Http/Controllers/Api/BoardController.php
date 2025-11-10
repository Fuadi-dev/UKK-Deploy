<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Card;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    /**
     * Get boards for a project (Leader only)
     */
    public function index(Request $request, $projectId)
    {
        $user = $request->user();
        
        // Check if user is leader of this project
        if (!$this->isLeaderOfProject($user, $projectId)) {
            return response()->json(['message' => 'Unauthorized. Leader only.'], 403);
        }

        $boards = Board::where('project_id', $projectId)
                      ->with(['cards.assignedUser', 'cards.subtasks'])
                      ->orderBy('position')
                      ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'boards' => $boards,
            ],
        ], 200);
    }

    /**
     * Create new board (Leader only)
     */
    public function store(Request $request, $projectId)
    {
        $user = $request->user();
        
        if (!$this->isLeaderOfProject($user, $projectId)) {
            return response()->json(['message' => 'Unauthorized. Leader only.'], 403);
        }

        $request->validate([
            'board_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $board = Board::create([
            'project_id' => $projectId,
            'board_name' => $request->board_name,
            'description' => $request->description,
            'position' => Board::where('project_id', $projectId)->count() + 1,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'board' => $board,
            ],
        ], 201);
    }

    /**
     * Create card in board (Leader only)
     */
    public function createCard(Request $request, $projectId, $boardId)
    {
        $user = $request->user();
        
        if (!$this->isLeaderOfProject($user, $projectId)) {
            return response()->json(['message' => 'Unauthorized. Leader only.'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'card_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        // Check if board exists and belongs to project
        $board = Board::where('id', $boardId)
                     ->where('project_id', $projectId)
                     ->first();

        if (!$board) {
            return response()->json(['message' => 'Board not found'], 404);
        }

        // Check if user is member of this project
        $isMember = ProjectMember::where('project_id', $projectId)
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
            'board_id' => $boardId,
            'user_id' => $request->user_id,
            'card_title' => $request->card_title,
            'description' => $request->description,
            'position' => Card::where('board_id', $boardId)->count() + 1,
            'due_date' => $request->due_date,
            'status' => 'in_progress',
            'priority' => $request->priority,
            'estimated_hours' => $request->estimated_hours,
            'actual_hours' => 0,
        ]);

        // Update user status to working
        $assignedUser->status = 'working';
        $assignedUser->save();

        $card->load('assignedUser', 'subtasks');

        return response()->json([
            'status' => 'success',
            'data' => [
                'card' => $card,
            ],
        ], 201);
    }

    /**
     * Get project members for card assignment
     */
    public function getProjectMembers(Request $request, $projectId)
    {
        $user = $request->user();
        
        if (!$this->isLeaderOfProject($user, $projectId)) {
            return response()->json(['message' => 'Unauthorized. Leader only.'], 403);
        }

        $members = ProjectMember::where('project_id', $projectId)
                               ->with('user:id,name,email,role,status')
                               ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'members' => $members,
            ],
        ], 200);
    }

    /**
     * Check if user is leader of project
     */
    private function isLeaderOfProject($user, $projectId)
    {
        if ($user->role === 'admin') {
            return true; // Admin can access all projects
        }

        return ProjectMember::where('project_id', $projectId)
                           ->where('user_id', $user->id)
                           ->where('role', 'leader')
                           ->exists() || $user->role === 'leader';
    }
}
