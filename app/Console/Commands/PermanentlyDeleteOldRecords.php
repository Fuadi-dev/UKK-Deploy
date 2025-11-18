<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Project;
use App\Models\Card;
use App\Models\Board;
use App\Models\Subtask;
use App\Models\Permission;
use Carbon\Carbon;

class PermanentlyDeleteOldRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:soft-deleted {--days=30 : Number of days before permanent deletion}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete soft-deleted records older than specified days (default: 30 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);
        
        $this->info("Starting cleanup of soft-deleted records older than {$days} days...");
        $this->info("Cutoff date: {$cutoffDate->format('Y-m-d H:i:s')}");
        $this->newLine();
        
        $totalDeleted = 0;
        
        // Array of models to clean up
        $models = [
            'User' => User::class,
            'Project' => Project::class,
            'Card' => Card::class,
            'Board' => Board::class,
            'Subtask' => Subtask::class,
            'Permission' => Permission::class,
        ];
        
        foreach ($models as $name => $modelClass) {
            try {
                // Check if model uses SoftDeletes
                if (!method_exists($modelClass, 'onlyTrashed')) {
                    $this->warn("Skipping {$name} - does not use soft deletes");
                    continue;
                }
                
                $deleted = $modelClass::onlyTrashed()
                    ->where('deleted_at', '<=', $cutoffDate)
                    ->forceDelete();
                
                $totalDeleted += $deleted;
                
                if ($deleted > 0) {
                    $this->info("✓ Permanently deleted {$deleted} {$name}(s)");
                } else {
                    $this->line("  No {$name}(s) to delete");
                }
            } catch (\Exception $e) {
                $this->error("✗ Error deleting {$name}: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Total records permanently deleted: {$totalDeleted}");
        $this->info("Cleanup completed successfully!");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        return Command::SUCCESS;
    }
}
