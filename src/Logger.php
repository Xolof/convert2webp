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
     * @param array $messageItem
     * @return void
     */
    public static function log(array $messageItem): void
    {
        $path             = plugin_dir_path(__DIR__);
        $logFile          = $path . 'c2w.log.json';

        if (file_exists($logFile)) {
            $logContent       = json_decode(file_get_contents($logFile));
        } else {
            $logContent = [];
        }

        $logContent[]     = $messageItem;
        file_put_contents($logFile, wp_json_encode($logContent));
    }

    /**
     * Delete the log file.
     *
     * @return void
     */
    public static function clear(): void
    {
        $path             = plugin_dir_path(__DIR__);
        $destination_file = $path . 'c2w.log.json';
        if (file_exists($destination_file)) {
            unlink($destination_file);
        }
    }
}
