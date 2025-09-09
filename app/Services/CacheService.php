<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Clear all home-related caches
     */
    public static function clearHomeCaches(): void
    {
        try {
            // Clear top headings caches
            Cache::forget('top_headings_guest');
            
            // Clear all user-specific caches (we'll use pattern matching)
            self::clearUserCaches();
            
            Log::info('Home caches cleared successfully');
        } catch (\Exception $e) {
            Log::error('Failed to clear home caches: ' . $e->getMessage());
        }
    }

    /**
     * Clear caches for a specific user
     */
    public static function clearUserCaches(?int $userId = null): void
    {
        try {
            if ($userId) {
                // Clear specific user caches
                Cache::forget("top_headings_user_{$userId}");
                Cache::forget("preferred_articles_user_{$userId}");
                Cache::forget("user_preferred_categories_{$userId}");
            } else {
                // Clear all user caches using pattern matching
                // Note: This requires Redis and may not work with all cache drivers
                self::clearCachesByPattern('top_headings_user_*');
                self::clearCachesByPattern('preferred_articles_user_*');
                self::clearCachesByPattern('user_preferred_categories_*');
            }
            
            Log::info('User caches cleared successfully', ['user_id' => $userId]);
        } catch (\Exception $e) {
            Log::error('Failed to clear user caches: ' . $e->getMessage(), ['user_id' => $userId]);
        }
    }

    /**
     * Clear caches by pattern (Redis specific)
     */
    private static function clearCachesByPattern(string $pattern): void
    {
        try {
            if (config('cache.default') === 'redis' && Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getRedis();
                $keys = $redis->keys($pattern);
                
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to clear caches by pattern: ' . $e->getMessage(), ['pattern' => $pattern]);
        }
    }

    /**
     * Clear article-related caches when articles are updated
     */
    public static function clearArticleCaches(): void
    {
        self::clearHomeCaches();
    }

    /**
     * Clear user interest caches when user interests change
     */
    public static function clearUserInterestCaches(int $userId): void
    {
        self::clearUserCaches($userId);
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        try {
            if (config('cache.default') === 'redis' && Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getRedis();
                $info = $redis->info();
                
                return [
                    'driver' => config('cache.default'),
                    'redis_version' => $info['redis_version'] ?? 'unknown',
                    'used_memory' => $info['used_memory_human'] ?? 'unknown',
                    'connected_clients' => $info['connected_clients'] ?? 'unknown',
                    'total_commands_processed' => $info['total_commands_processed'] ?? 'unknown',
                ];
            }
            
            return [
                'driver' => config('cache.default'),
                'status' => 'Cache driver does not support statistics'
            ];
        } catch (\Exception $e) {
            return [
                'driver' => config('cache.default'),
                'error' => $e->getMessage()
            ];
        }
    }
}
