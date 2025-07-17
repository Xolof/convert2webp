<?php

/**
 * Custom logger
 *
 * @package Convert2Webp
 */

namespace Convert2Webp;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * c2w Custom Logger
 */
class Logger
{
    /**
     * Write message to a log file.
     *
     * @param string $message
     * @return void
     */
    public static function log(string $message): void
    {
        $path             = plugin_dir_path(__DIR__);
        $message          = wp_json_encode($message) . "\n";
        $destination_file = $path . 'c2w.log';
        error_log(htmlspecialchars($message), 3, $destination_file); // phpcs:ignore
    }

    /**
     * Delete the log file.
     *
     * @return void
     */
    public static function clear(): void
    {
        $path             = plugin_dir_path(__DIR__);
        $destination_file = $path . 'c2w.log';
        unlink($destination_file);
    }
}
