<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\BoardController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TimeLogController;
use App\Http\Controllers\Api\PermissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'googleLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    // User search endpoint
    Route::get('/users/search', function(Request $request) {
        $search = $request->get('search', '');
        $role = $request->get('role', '');
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }
        
        $query = \App\Models\User::where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%");
            
        // Filter by role if specified
        if (!empty($role)) {
            $query->where('role', $role);
        }
            
        $users = $query->select('id', 'name', 'email', 'role', 'avatar')
            ->limit(10)
            ->get();
            
        // Add avatar URL and initials for each user
        $users = $users->map(function ($user) {
            // Check if user is assigned to any project
            $isWorking = \App\Models\ProjectMember::where('user_id', $user->id)->exists();
            
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'avatar_url' => $user->getAvatarUrl(),
                'initials' => $user->getInitials(),
                'is_working' => $isWorking,
                'status' => $isWorking ? 'working' : 'available'
            ];
        });
            
        return response()->json($users);
    });
    
    // Legacy route for backward compatibility
    Route::get('/tasks', [TaskController::class, 'card']);
    
    // Project Management (Admin only)
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::post('/', [ProjectController::class, 'store']);
        Route::post('/{project}/members', [ProjectController::class, 'addMember']);
        Route::delete('/{project}/members/{member}', [ProjectController::class, 'removeMember']);
        Route::get('/{project}/members', [ProjectController::class, 'getMembers']);
        Route::get('/available-users', [ProjectController::class, 'getAvailableUsers']);
    });
    
    // Board Management (Leader only)
    Route::prefix('projects/{project}/boards')->group(function () {
        Route::get('/', [BoardController::class, 'index']);
        Route::post('/', [BoardController::class, 'store']);
        Route::post('/{board}/cards', [BoardController::class, 'createCard']);
        Route::get('/members', [BoardController::class, 'getProjectMembers']);
    });
    
    // Task Management (Developer/Designer)
    Route::prefix('tasks')->group(function () {
        // Card endpoints
        Route::get('/my-card', [TaskController::class, 'getMyCard']);
        Route::get('/cards/{card}', [TaskController::class, 'getCardDetail']);
        Route::post('/cards/{card}/start', [TaskController::class, 'startCard']);
        Route::put('/cards/{card}/status', [TaskController::class, 'updateCardStatus']);
        Route::get('/cards/{card}/comments', [TaskController::class, 'getCardComments']);
        Route::post('/cards/{card}/comments', [TaskController::class, 'addCardComment']);
        
        // Subtask endpoints
        Route::get('/subtasks', [TaskController::class, 'getSubtasks']);
        Route::get('/subtasks/{subtask}', [TaskController::class, 'getSubtaskDetail']);
        Route::post('/subtasks', [TaskController::class, 'createSubtask']);
        Route::put('/subtasks/{subtask}', [TaskController::class, 'updateSubtask']);
        Route::put('/subtasks/{subtask}/toggle', [TaskController::class, 'toggleSubtaskStatus']);
        Route::delete('/subtasks/{subtask}', [TaskController::class, 'deleteSubtask']);
        Route::get('/subtasks/{subtask}/comments', [TaskController::class, 'getSubtaskComments']);
        Route::post('/subtasks/{subtask}/comments', [TaskController::class, 'addSubtaskComment']);
    });
    
    // Time Log Management (User & Leader)
    Route::prefix('time-logs')->group(function () {
        // User endpoints - subtask time logs only
        Route::get('/', [TimeLogController::class, 'index']);
        Route::get('/{id}', [TimeLogController::class, 'show']);
        
        // Leader endpoints - card time logs only
        Route::get('/team/list', [TimeLogController::class, 'leaderIndex']);
        Route::get('/team/{id}', [TimeLogController::class, 'leaderShow']);
    });
    
    // Permission Management (User & Leader)
    Route::prefix('permissions')->group(function () {
        // User endpoints - own permission requests
        Route::get('/', [PermissionController::class, 'index']);
        Route::get('/{id}', [PermissionController::class, 'show']);
        Route::post('/', [PermissionController::class, 'store']);
        Route::put('/{id}', [PermissionController::class, 'update']);
        Route::delete('/{id}', [PermissionController::class, 'destroy']);
        
        // Leader endpoints - team permission requests
        Route::get('/team/list', [PermissionController::class, 'leaderIndex']);
        Route::get('/team/{id}', [PermissionController::class, 'leaderShow']);
    });
    
});