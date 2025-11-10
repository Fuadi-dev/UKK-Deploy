<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Board;
use App\Models\Card;
use App\Models\Subtask;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProjectManagementSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'free',
        ]);

        // Create Leader
        $leader = User::create([
            'name' => 'Project Leader',
            'email' => 'leader@example.com',
            'password' => Hash::make('password'),
            'role' => 'leader',
            'status' => 'free',
        ]);

        // Create Developer
        $developer = User::create([
            'name' => 'John Developer',
            'email' => 'developer@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'free',
        ]);

        // Create Designer
        $designer = User::create([
            'name' => 'Jane Designer',
            'email' => 'designer@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'free',
        ]);

        // Create all user
        $user = User::create([
            'name' => 'Ambatron',
            'email' => 'amba@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'free',
        ]);
        
        // Create Project
        $project = Project::create([
            'user_id' => $leader->id,
            'project_name' => 'Mobile App Development',
            'slug' => 'mobile-app-development',
            'description' => 'A project to develop a mobile application',
            'deadline' => now()->addMonths(3),
            'status' => 'active',
        ]);

        // Add members to project
        ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $leader->id,
            'role' => 'Project Manager',
            'joined_at' => now(),
        ]);

        ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $developer->id,
            'role' => 'Developer',
            'joined_at' => now(),
        ]);

        ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $designer->id,
            'role' => 'Designer',
            'joined_at' => now(),
        ]);

        // Create Boards
        $todoBoard = Board::create([
            'project_id' => $project->id,
            'board_name' => 'To Do',
            'description' => 'Tasks to be done',
            'position' => 1,
        ]);

        $inProgressBoard = Board::create([
            'project_id' => $project->id,
            'board_name' => 'In Progress',
            'description' => 'Tasks currently being worked on',
            'position' => 2,
        ]);

        $reviewBoard = Board::create([
            'project_id' => $project->id,
            'board_name' => 'Review',
            'description' => 'Tasks waiting for review',
            'position' => 3,
        ]);

        $doneBoard = Board::create([
            'project_id' => $project->id,
            'board_name' => 'Done',
            'description' => 'Completed tasks',
            'position' => 4,
        ]);

        // Create Card for Developer
        $devCard = Card::create([
            'board_id' => $todoBoard->id,
            'user_id' => $developer->id,
            'card_title' => 'Implement User Authentication',
            'slug' => 'implement-user-authentication',
            'description' => 'Create login and registration functionality',
            'position' => 1,
            'due_date' => now()->addWeeks(2),
            'status' => 'todo',
            'priority' => 'high',
            'estimated_hours' => 40,
            'actual_hours' => 0,
        ]);

        // Update developer status
        $developer->status = 'working';
        $developer->save();

        // Create Subtasks for Developer Card
        Subtask::create([
            'card_id' => $devCard->id,
            'user_id' => $developer->id,
            'subtask_title' => 'Setup authentication routes',
            'description' => 'Create API routes for login, register, logout',
            'status' => 'done',
            'estimated_hours' => 8,
            'actual_hours' => 6,
            'position' => 1,
        ]);

        Subtask::create([
            'card_id' => $devCard->id,
            'user_id' => $developer->id,
            'subtask_title' => 'Create login form',
            'description' => 'Design and implement login UI',
            'status' => 'in_progress',
            'estimated_hours' => 12,
            'actual_hours' => 0,
            'position' => 2,
        ]);

        Subtask::create([
            'card_id' => $devCard->id,
            'user_id' => $developer->id,
            'subtask_title' => 'Implement JWT token handling',
            'description' => 'Handle token storage and validation',
            'status' => 'in_progress',
            'estimated_hours' => 10,
            'actual_hours' => 0,
            'position' => 3,
        ]);

        // Create Card for Designer
        $designCard = Card::create([
            'board_id' => $todoBoard->id,
            'user_id' => $designer->id,
            'card_title' => 'Design App UI/UX',
            'slug' => 'design-app-ui-ux',
            'description' => 'Create user interface designs for the mobile app',
            'position' => 2,
            'due_date' => now()->addWeeks(3),
            'status' => 'todo',
            'priority' => 'medium',
            'estimated_hours' => 60,
            'actual_hours' => 0,
        ]);

        // Update designer status
        $designer->status = 'working';
        $designer->save();

        // Create Subtasks for Designer Card
        Subtask::create([
            'card_id' => $designCard->id,
            'user_id' => $designer->id,
            'subtask_title' => 'Create wireframes',
            'description' => 'Design initial wireframes for all screens',
            'status' => 'done',
            'estimated_hours' => 20,
            'actual_hours' => 18,
            'position' => 1,
        ]);

        Subtask::create([
            'card_id' => $designCard->id,
            'user_id' => $designer->id,
            'subtask_title' => 'Design login screen',
            'description' => 'Create high-fidelity design for login screen',
            'status' => 'in_progress',
            'estimated_hours' => 15,
            'actual_hours' => 0,
            'position' => 2,
        ]);

        Subtask::create([
            'card_id' => $designCard->id,
            'user_id' => $designer->id,
            'subtask_title' => 'Design dashboard',
            'description' => 'Create dashboard layout and components',
            'status' => 'in_progress',
            'estimated_hours' => 25,
            'actual_hours' => 0,
            'position' => 3,
        ]);


    }
}
