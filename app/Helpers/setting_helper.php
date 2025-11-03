<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Get setting value by group and key
 *
 * Example:
 * setting('lead', 'sources');
 * setting('lead', 'sources.social_media');
 */
if (!function_exists('setting')) {
    function setting(string $group, ?string $key = null, $default = null)
    {
        // Cache all settings for better performance (5 minutes)
        $settings = Cache::remember('app_settings', 300, function () {
            return App\Models\Setting::all()->groupBy('group');
        });

        if (!$settings->has($group)) {
            return $default;
        }

        $groupSettings = $settings[$group]->keyBy('key');

        // Return all settings for a group if key is not provided
        if ($key === null) {
            return $groupSettings;
        }

        // Handle dot notation (e.g., "sources.social_media")
        $parts = explode('.', $key);
        $mainKey = array_shift($parts);

        if (!isset($groupSettings[$mainKey])) {
            return $default;
        }

        $value = $groupSettings[$mainKey]->value;

        foreach ($parts as $part) {
            if (is_array($value) && isset($value[$part])) {
                $value = $value[$part];
            } else {
                return $default;
            }
        }

        return $value;
    }
}
