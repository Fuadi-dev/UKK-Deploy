<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Leader\BoardController;
use App\Http\Controllers\Leader\CardController;
use App\Http\Controllers\Leader\PermissionController;
use App\Http\Controllers\User\SubtaskController;
use App\Http\Controllers\Leader\ProjectController as LeaderProjectController;
use App\Http\Controllers\Leader\TimeLogController as LeaderTimeLogController;
use App\Http\Controllers\User\CardController as UserCardController;
use App\Http\Controllers\User\PermissionController as UserPermissionController;
use App\Http\Controllers\User\TimeLogController;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Google OAuth routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
});


// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::middleware('role:admin,leader,user')->group(function () {
        Route::get('/dashboard', [MainController::class, 'dashboard'])->name('dashboard');
        
        // User management routes
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    });


    //Route Role Leader
    Route::middleware('role:leader')->group(function () {
        // Leader routes - My Projects
        Route::get('/my-projects', [LeaderProjectController::class, 'myProjects'])->name('leader.projects');
        Route::get('/my-projects/{slug}', [LeaderProjectController::class, 'showProject'])->name('leader.projects.show');
        Route::put('/my-projects/{project}/status', [LeaderProjectController::class, 'updateStatus'])->name('leader.projects.updateStatus');
        
        // Leader routes - Boards Management (Read Only)
        Route::get('/boards', [BoardController::class, 'index'])->name('leader.boards');
        
        // Leader routes - Cards Management
        Route::get('/cards', [CardController::class, 'index'])->name('leader.cards');
        Route::post('/cards', [CardController::class, 'store'])->name('leader.cards.store');
        Route::get('/cards/{card}', [CardController::class, 'show'])->name('leader.cards.show');
        Route::put('/cards/{card}', [CardController::class, 'update'])->name('leader.cards.update');
        Route::delete('/cards/{card}', [CardController::class, 'destroy'])->name('leader.cards.destroy');
        Route::post('/cards/{card}/comments', [CardController::class, 'addComment'])->name('leader.cards.comments.store');
        Route::get('/cards/{card}/comments', [CardController::class, 'getComments'])->name('leader.cards.comments');
        Route::get('/project-members', [CardController::class, 'getProjectMembers'])->name('leader.project.members');
        
        // Leader routes - Assigned Cards (Review Status)
        Route::get('/assigned-cards', [CardController::class, 'assignedCards'])->name('leader.assigned.cards');
        Route::post('/cards/{card}/approve', [CardController::class, 'approveCard'])->name('leader.cards.approve');
        Route::post('/cards/{card}/reject', [CardController::class, 'rejectCard'])->name('leader.cards.reject');
        
        // Leader routes - Assignment History
        Route::get('/assignment-history', [CardController::class, 'assignmentHistory'])->name('leader.assignment.history');

        // Leader routes - Card Time Log Management (View Only)
        Route::get('/leader/time-logs', [LeaderTimeLogController::class, 'index'])->name('leader.time-logs');
        Route::get('/leader/time-logs/{timeLog}', [LeaderTimeLogController::class, 'show'])->name('leader.time-logs.show');
        
        // Leader routes - Permission Management
        Route::get('/leader/permissions', [PermissionController::class, 'index'])->name('leader.permissions');
        Route::post('/leader/permissions', [PermissionController::class, 'store'])->name('leader.permissions.store');
        Route::post('/leader/permissions/{permission}/approve', [PermissionController::class, 'approve'])->name('leader.permissions.approve');
        Route::post('/leader/permissions/{permission}/reject', [PermissionController::class, 'reject'])->name('leader.permissions.reject');
        Route::delete('/leader/permissions/{permission}', [PermissionController::class, 'destroy'])->name('leader.permissions.destroy');
    });

    //route role user
    Route::middleware('role:user')->group(function () {
        // User routes - My Card Management
        Route::get('/my-card', [UserCardController::class, 'myCard'])->name('user.my-card');
        Route::post('/my-card/{card}/start', [UserCardController::class, 'startCard'])->name('user.card.start');
        Route::post('/my-card/{card}/review', [UserCardController::class, 'updateToReview'])->name('user.card.review');
        Route::get('/my-card/{card}', [UserCardController::class, 'show'])->name('user.card.show');
        Route::post('/my-card/{card}/comments', [UserCardController::class, 'addComment'])->name('user.card.comments.store');
        Route::get('/my-card/{card}/comments', [UserCardController::class, 'getComments'])->name('user.card.comments');
        
        // User routes - Subtask Management
        Route::get('/subtasks', [SubtaskController::class, 'index'])->name('user.subtasks');
        Route::post('/subtasks', [SubtaskController::class, 'store'])->name('user.subtasks.store');
        Route::get('/subtasks/{subtask}', [SubtaskController::class, 'show'])->name('user.subtasks.show');
        Route::put('/subtasks/{subtask}', [SubtaskController::class, 'update'])->name('user.subtasks.update');
        Route::patch('/subtasks/{subtask}/status', [SubtaskController::class, 'updateStatus'])->name('user.subtasks.status');
        Route::delete('/subtasks/{subtask}', [SubtaskController::class, 'destroy'])->name('user.subtasks.destroy');
        Route::get('/subtasks/{subtask}/comments', [SubtaskController::class, 'getComments'])->name('user.subtasks.comments');
        Route::post('/subtasks/{subtask}/comments', [SubtaskController::class, 'addComment'])->name('user.subtasks.comments.store');
        
        // User routes - Permission Management
        Route::get('/user/permissions', [UserPermissionController::class, 'index'])->name('user.permissions');
        Route::post('/user/permissions', [UserPermissionController::class, 'store'])->name('user.permissions.store');
        Route::get('/user/permissions/{permission}', [UserPermissionController::class, 'show'])->name('user.permissions.show');
        Route::put('/user/permissions/{permission}', [UserPermissionController::class, 'update'])->name('user.permissions.update');
        Route::delete('/user/permissions/{permission}', [UserPermissionController::class, 'destroy'])->name('user.permissions.destroy');
        
        // User routes - Time Log Management (View Only - Automatic Tracking)
        Route::get('/user/time-logs', [TimeLogController::class, 'index'])->name('user.time-logs');
        Route::get('/user/time-logs/{timeLog}', [TimeLogController::class, 'show'])->name('user.time-logs.show');
            
    });


    //route role admin
    Route::middleware('role:admin')->group(function () {
        // Project routes
        Route::get('/projects', [ProjectController::class, 'getProjects'])->name('projects');
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('/projects/{slug}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{slug}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{id}', [ProjectController::class, 'deleteProject'])->name('projects.delete');

        // Project member management
        Route::get('/api/users/search', [ProjectController::class, 'searchUsers'])->name('users.search');
        Route::post('/projects/{slug}/members', [ProjectController::class, 'addMember'])->name('projects.addMember');
        Route::delete('/projects/{slug}/members', [ProjectController::class, 'removeMember'])->name('projects.removeMember');

        // Project status management
        Route::post('/projects/{project}/complete', [ProjectController::class, 'completeProject'])->name('projects.complete');
        Route::post('/projects/{project}/cancel', [ProjectController::class, 'cancelProject'])->name('projects.cancel');
        Route::post('/projects/{project}/reactivate', [ProjectController::class, 'reactivateProject'])->name('projects.reactivate');
        Route::put('/projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.updateStatus');

        // Admin only - User management
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Reports routes
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    });    
});