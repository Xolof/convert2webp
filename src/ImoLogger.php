<?php
/**
 * Custom logger
 *
 * @package Forminator Voting System
 */

namespace ImageOptimizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * imo Custom Logger
 */
class ImoLogger {
	/**
	 * Write message to a log file.
	 *
	 * @param string $message
	 * @return void
	 */
	public static function log( string $message ): void {
		$path             = plugin_dir_path( __DIR__ );
		$message          = wp_json_encode( $message ) . "\n";
		$destination_file = $path . 'imo.log';
		error_log( $message, 3, $destination_file ); // phpcs:ignore
	}
}
