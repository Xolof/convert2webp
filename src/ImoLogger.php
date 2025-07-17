<?php

/**
 * Custom logger
 *
 * @package Image Optimizer
 */

namespace ImageOptimizer;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * imo Custom Logger
 */
class ImoLogger
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
        $destination_file = $path . 'imo.log';
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
        $destination_file = $path . 'imo.log';
        unlink($destination_file);
    }
}
