<?php

/**
 * MenuManager
 *
 * @package Convert2Webp
 */

namespace Convert2Webp;

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
            esc_html__('Convert 2 Webp', 'c2w'),
            esc_html__('Convert 2 Webp', 'c2w'),
            'manage_options',
            'c2w',
            array( $this, 'renderC2wPage' ),
            'dashicons-images-alt2',
            3
        );
        add_submenu_page(
            'c2w',
            esc_html__('Convert 2 Webp', 'c2w'),
            esc_html__('Convert 2 Webp', 'c2w'),
            'manage_options',
            'renderC2wPage',
            array( $this, 'renderC2wPage' )
        );
        remove_submenu_page('c2w', 'c2w');
    }

    /**
     * Get the images and render the admin page.
     *
     * @return void
     */
    public function renderC2wPage(): void
    {
        $images = $this->results_fetcher->getImages();
        require_once __DIR__ . '/../templates/page.php';
    }
}
