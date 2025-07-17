<?php

/**
 * Main class.
 *
 * @package Image_Optimizer
 */

namespace ImageOptimizer;

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
        $this->loadTextdomain();
        $this->addMenuPages();
        $this->process();
        $this->addStyles();
        $this->registerDeactivationHook();
    }

    /**
     * Process the settings from the admin page.
     *
     * @return void
     */
    protected function process(): void
    {
        add_action('rest_api_init', function () {
            register_rest_route('imo/v1', '/convert/', [
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
                    'imo-plugin-admin-styles',
                    plugins_url('../assets/css/imo-admin-styles.css', __FILE__),
                    array(),
                    '1.0',
                    'all'
                );

                wp_enqueue_script(
                    'imo-ajax',
                    plugins_url('../assets/js/imo-ajax.js', __FILE__),
                    [],
                    '1.0',
                    true
                );

                wp_localize_script('imo-ajax', 'imageOptimizer', [
                    'nonce' => wp_create_nonce('wp_rest'),
                    'pluginsUrl' => plugins_url(),
                ]);
            }
        );
    }

    /**
     * Load the plugin's textdomain which is needed for translations.
     *
     * @return void
     */
    protected function loadTextdomain(): void
    {
        add_action(
            'plugins_loaded',
            function () {
                $path   = plugin_dir_path(__DIR__) . 'languages';
                $loaded = load_plugin_textdomain('imo', false, plugin_basename($path));
            },
            5
        );
    }

    /**
     * Register actions to execute on deactivation of the plugin.
     *
     * @return void
     */
    protected function registerDeactivationHook(): void
    {
        $main_plugin_file = plugin_dir_path(__DIR__) . 'image-optimizer.php';

        register_deactivation_hook(
            plugin_basename($main_plugin_file),
            function () {
                $options = array(
                    'imo_settings',
                    'imo_db_version',
                );

                foreach ($options as $option) {
                    if (get_option($option)) {
                        delete_option($option);
                    }
                }
            }
        );
    }
}
