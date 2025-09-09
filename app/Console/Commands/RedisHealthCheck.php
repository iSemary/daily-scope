<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class RedisHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:health-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Redis connectivity and performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Redis Health Check');
        $this->info('====================');

        // Check Redis connection
        try {
            $ping = Redis::ping();
            if ($ping === 'PONG' || $ping === true) {
                $this->info('✅ Redis is running and responding');
            } else {
                $this->error('❌ Redis is not responding properly');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Redis connection failed: ' . $e->getMessage());
            return 1;
        }

        // Get Redis info
        $this->newLine();
        $this->info('📊 Redis Information:');
        $this->info('-------------------');
        
        try {
            $info = Redis::info('server');
            $this->line('Redis Version: ' . ($info['redis_version'] ?? 'Unknown'));
            $this->line('Uptime: ' . ($info['uptime_in_seconds'] ?? 'Unknown') . ' seconds');
            $this->line('Connected Clients: ' . ($info['connected_clients'] ?? 'Unknown'));

            $memory = Redis::info('memory');
            $this->line('Used Memory: ' . ($memory['used_memory_human'] ?? 'Unknown'));
            $this->line('Max Memory: ' . ($memory['maxmemory_human'] ?? 'No limit'));
            $this->line('Peak Memory: ' . ($memory['used_memory_peak_human'] ?? 'Unknown'));
        } catch (\Exception $e) {
            $this->warn('Could not retrieve Redis info: ' . $e->getMessage());
        }

        // Check cache keys
        $this->newLine();
        $this->info('🗝️ Cache Keys:');
        $this->info('-------------');
        
        try {
            $allKeys = Redis::keys('*');
            $appKeys = Redis::keys('daily-scope-cache-*');
            
            $this->line('Total keys: ' . count($allKeys));
            $this->line('Application cache keys: ' . count($appKeys));
        } catch (\Exception $e) {
            $this->warn('Could not retrieve key count: ' . $e->getMessage());
        }

        // Test cache operations
        $this->newLine();
        $this->info('🧪 Testing Cache Operations:');
        $this->info('---------------------------');
        
        try {
            $testKey = 'redis_health_test_' . time();
            $testValue = 'test_value_' . uniqid();
            
            // Test write
            Cache::put($testKey, $testValue, 60);
            
            // Test read
            $retrievedValue = Cache::get($testKey);
            
            if ($retrievedValue === $testValue) {
                $this->info('✅ Cache write/read test passed');
            } else {
                $this->error('❌ Cache write/read test failed');
                return 1;
            }
            
            // Clean up
            Cache::forget($testKey);
            
        } catch (\Exception $e) {
            $this->error('❌ Cache operations test failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info('🎉 Redis health check completed successfully!');
        
        return 0;
    }
}
