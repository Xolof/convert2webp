<?php

/**
 * Main class.
 *
 * @package Convert2Webp
 */

namespace Convert2Webp;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Optimizer
 *
 * The main class for the plugin.
 */
class Optimizer
{
    protected Converter $converter;
    protected MenuManager $menu_manager;

    /**
     * Constructor
     *
     * @param Converter    $converter
     * @param MenuManager  $menu_manager
     */
    public function __construct(
        Converter $converter,
        MenuManager $menu_manager,
    ) {
        $this->converter    = $converter;
        $this->menu_manager = $menu_manager;
    }

    /**
     * Start up the plugin.
     *
     * @return void
     */
    public function init(): void
    {
        $this->addMenuPages();
        $this->process();
        $this->addStyles();
    }

    /**
     * Process the input from the admin page.
     *
     * @return void
     */
    protected function process(): void
    {
        add_action('rest_api_init', function () {
            register_rest_route('c2w/v1', '/convert/', [
                'methods' => 'GET',
                'callback' => function () {
                    $this->converter->convert();
                    return new \WP_REST_Response(['message' => 'Image processing finished.'], 200);
                },
                'permission_callback' => function () {
                    if (is_user_logged_in() && current_user_can('edit_posts')) {
                        return true;
                    }
                    return new \WP_Error('rest_forbidden', 'Permission denied.', ['status' => 401]);
                },
            ]);
        });
    }

    /**
     * Add menu pages in admin interface.
     *
     * @return void
     */
    protected function addMenuPages(): void
    {
        add_action('admin_menu', array($this->menu_manager, 'addMenuPages'));
    }

    /**
     * Add custom stylesheet.
     *
     * @return void
     */
    protected function addStyles(): void
    {
        add_action(
            'admin_enqueue_scripts',
            function () {
                wp_enqueue_style(
                    'c2w-plugin-admin-styles',
                    plugins_url('../assets/css/c2w-admin-styles.css', __FILE__),
                    array(),
                    '1.0',
                    'all'
                );

                wp_enqueue_script(
                    'c2w-ajax',
                    plugins_url('../assets/js/c2w-ajax.js', __FILE__),
                    [],
                    '1.0',
                    true
                );

                wp_localize_script('c2w-ajax', 'Convert2Webp', [
                    'nonce' => wp_create_nonce('wp_rest'),
                    'pluginsUrl' => plugins_url(),
                ]);
            }
        );
    }
}
