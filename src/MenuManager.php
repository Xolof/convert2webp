<?php
/**
 * MenuManager
 *
 * @package Image_Optimizer
 */

namespace ImageOptimizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu Manager
 */
class MenuManager {

	protected ResultsFetcher $results_fetcher;

	/**
	 * Constructor
	 *
	 * @param ResultsFetcher $results_fetcher
	 */
	public function __construct( ResultsFetcher $results_fetcher ) {
		$this->results_fetcher = $results_fetcher;
	}

	/**
	 * Add menu pages.
	 *
	 * @return void
	 */
	public function add_menu_pages(): void {
		add_menu_page(
			esc_html__( 'Image Optimizer', 'imo' ),
			esc_html__( 'Image Optimizer', 'imo' ),
			'manage_options',
			'imo',
			array( $this, 'render_imo_settings' ),
			'dashicons-images-alt2',
			3
		);
		add_submenu_page(
			'imo',
			esc_html__( 'Image Optimizer', 'imo' ),
			esc_html__( 'Image Optimizer', 'imo' ),
			'manage_options',
			'render_imo_settings',
			array( $this, 'render_imo_settings' )
		);
		remove_submenu_page( 'imo', 'imo' );
	}

	/**
	 * Get the Forminator forms and render the settings page.
	 *
	 * @return void
	 */
	public function render_imo_settings(): void {
		$images = $this->results_fetcher->get_images();
		require_once __DIR__ . '/../templates/settings.php';
	}
}
