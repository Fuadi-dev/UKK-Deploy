<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProjectStatusService;

class UpdateProjectStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update project status and free users from expired/completed projects';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting project status update...');
        
        try {
            ProjectStatusService::updateProjectAndUserStatus();
            
            $stats = ProjectStatusService::getProjectStatusStats();
            
            $this->info('Project status update completed successfully!');
            $this->table(
                ['Status', 'Count'],
                [
                    ['Active', $stats['active']],
                    ['Completed', $stats['completed']],
                    ['Expired', $stats['expired']],
                    ['Cancelled', $stats['cancelled']],
                    ['On Hold', $stats['on_hold']],
                ]
            );
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error updating project status: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
