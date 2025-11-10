<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    /**
     * Display boards for leader's assigned project
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get the project where the current user is assigned as a member
        $project = Project::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['boards.cards.assignedUser', 'boards.cards.subtasks', 'members.user'])->first();

        if (!$project) {
            return view('pages.leader.board.boards', ['project' => null, 'boards' => collect()]);
        }

        $boards = $project->boards()->with(['cards.assignedUser', 'cards.subtasks'])->orderBy('position')->get();

        return view('pages.leader.board.boards', compact('project', 'boards'));
    }
}
