<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\CacheManager;

class Cache
{
    protected static $table;

    public static function init(): void
    {
        global $wpdb;
        self::$table = $wpdb->prefix . 'mls_on_the_fly_cache';
    }

    /**
     * Set a cache item with support for persistent caching.
     *
     * @param string $key The cache key.
     * @param mixed $value The value to be cached.
     * @param int $expiration Expiration time in seconds.
     * @return bool
     */
    public static function set(string $key, mixed $value, int $expiration = 0): bool
    {
        // Use persistent cache if available
        if (wp_using_ext_object_cache()) {
            wp_cache_set($key, $value, '', $expiration);
            return true;
        }
        // Fallback to database cache if no persistent cache is available
        global $wpdb;
        self::init();

        $expires_at = $expiration > 0 ? date('Y-m-d H:i:s', current_time('timestamp') + $expiration) : null;

        $cache_value = maybe_serialize($value);
        $wpdb->replace(
            self::$table,
            [
                'cache_key' => $key,
                'cache_value' => $cache_value,
                'expires_at' => $expires_at,
                'created_at' => date('Y-m-d H:i:s', current_time('timestamp')),
                'updated_at' => date('Y-m-d H:i:s', current_time('timestamp')),
            ],
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            ]
        );

        return true;
    }

    /**
     * Get a cache item by key, checking persistent cache first.
     *
     * @param string $key The cache key.
     * @return mixed|null The cached value or null if not found.
     */
    public static function get(string $key): mixed
    {
        // Check persistent cache first
        if (wp_using_ext_object_cache()) {
            $value = wp_cache_get($key);
            if ($value !== false) {
                return $value;
            }
        }

        // Fallback to database if not in persistent cache
        global $wpdb;
        self::init();

        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT cache_value, expires_at FROM " . self::$table . " WHERE cache_key = %s",
                $key
            )
        );

        if ($result) {
            // Check if expired
            if ($result->expires_at && strtotime($result->expires_at) < current_time('timestamp')) {
                self::delete($key);
                return null;
            }
            return maybe_unserialize($result->cache_value);
        }

        return null;
    }

    /**
     * Delete a cache item by key from both persistent cache and database.
     *
     * @param string $key The cache key.
     * @return bool
     */
    public static function delete(string $key): bool
    {
        // Remove from persistent cache if available
        if (wp_using_ext_object_cache()) {
            wp_cache_delete($key);
        }

        // Fallback to remove from database if persistent cache is not used
        global $wpdb;
        self::init();

        return (bool)$wpdb->delete(
            self::$table,
            ['cache_key' => $key],
            ['%s']
        );
    }

    /**
     * Clear all expired cache items from the database.
     *
     * @return void
     */
    public static function clearExpired(): void
    {
        global $wpdb;
        self::init();

        $wpdb->query(
            "DELETE FROM " . self::$table . " WHERE expires_at IS NOT NULL AND expires_at < " . date(
                'Y-m-d H:i:s',
                current_time('timestamp')
            )
        );
    }
}
