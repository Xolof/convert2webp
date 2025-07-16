<?php

/**
 * MenuManager
 *
 * @package Image_Optimizer
 */

namespace ImageOptimizer;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Menu Manager
 */
class MenuManager
{
    protected ResultsFetcher $results_fetcher;

    /**
     * Constructor
     *
     * @param ResultsFetcher $results_fetcher
     */
    public function __construct(ResultsFetcher $results_fetcher)
    {
        $this->results_fetcher = $results_fetcher;
    }

    /**
     * Add menu pages.
     *
     * @return void
     */
    public function addMenuPages(): void
    {
        add_menu_page(
            esc_html__('Image Optimizer', 'imo'),
            esc_html__('Image Optimizer', 'imo'),
            'manage_options',
            'imo',
            array( $this, 'renderImoSettings' ),
            'dashicons-images-alt2',
            3
        );
        add_submenu_page(
            'imo',
            esc_html__('Image Optimizer', 'imo'),
            esc_html__('Image Optimizer', 'imo'),
            'manage_options',
            'renderImoSettings',
            array( $this, 'renderImoSettings' )
        );
        remove_submenu_page('imo', 'imo');
    }

    /**
     * Get the images and render the settings page.
     *
     * @return void
     */
    public function renderImoSettings(): void
    {
        $images = $this->results_fetcher->getImages();
        require_once __DIR__ . '/../templates/settings.php';
    }
}
