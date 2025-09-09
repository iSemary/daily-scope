<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

class ClearHomeCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-home {--user= : Clear cache for specific user ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear home-related caches (top-headlines and preferred articles)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user');
        
        if ($userId) {
            $this->info("Clearing caches for user ID: {$userId}");
            CacheService::clearUserCaches((int) $userId);
            $this->info("User caches cleared successfully!");
        } else {
            $this->info("Clearing all home-related caches...");
            CacheService::clearHomeCaches();
            $this->info("All home caches cleared successfully!");
        }
        
        return Command::SUCCESS;
    }
}
