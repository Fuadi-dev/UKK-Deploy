<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Permission::create([
            'user_id' => 3, // John Developer
            'project_id' => 1,
            'permission_type' => 'sick_leave',
            'reason' => 'Feeling unwell with flu symptoms. Need to rest and recover.',
            'start_date' => '2025-09-15',
            'end_date' => '2025-09-17',
            'duration_hours' => 24,
            'status' => 'pending',
        ]);

        \App\Models\Permission::create([
            'user_id' => 3, // John Developer
            'project_id' => 1,
            'permission_type' => 'vacation',
            'reason' => 'Annual family vacation trip to Bali.',
            'start_date' => '2025-10-01',
            'end_date' => '2025-10-07',
            'duration_hours' => 40,
            'status' => 'approved',
            'approved_by' => 2, // Leader
            'approved_at' => now(),
            'admin_notes' => 'Vacation approved. Enjoy your trip!',
        ]);

        \App\Models\Permission::create([
            'user_id' => 4, // Jane Designer
            'project_id' => 1,
            'permission_type' => 'personal_leave',
            'reason' => 'Attending wedding ceremony of my cousin.',
            'start_date' => '2025-09-20',
            'end_date' => '2025-09-20',
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'duration_hours' => 2,
            'status' => 'pending',
        ]);

        \App\Models\Permission::create([
            'user_id' => 4, // Jane Designer
            'project_id' => 1,
            'permission_type' => 'emergency',
            'reason' => 'Family emergency - father hospitalized.',
            'start_date' => '2025-09-10',
            'end_date' => '2025-09-11',
            'duration_hours' => 16,
            'status' => 'approved',
            'approved_by' => 2, // Leader
            'approved_at' => now()->subDays(2),
            'admin_notes' => 'Emergency leave approved. Hope everything turns out well.',
        ]);

        \App\Models\Permission::create([
            'user_id' => 5, // Ambatron
            'project_id' => 1,
            'permission_type' => 'other',
            'reason' => 'Attending mandatory training course for professional certification.',
            'start_date' => '2025-09-25',
            'end_date' => '2025-09-26',
            'duration_hours' => 16,
            'status' => 'rejected',
            'approved_by' => 2, // Leader
            'approved_at' => now()->subDay(),
            'admin_notes' => 'Training can be scheduled outside work hours.',
        ]);
    }
}
