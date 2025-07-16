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
    protected Processor $processor;
    protected MenuManager $menu_manager;

    /**
     * Constructor
     *
     * @param Processor    $processor
     * @param MenuManager  $menu_manager
     */
    public function __construct(
        Processor $processor,
        MenuManager $menu_manager,
    ) {
        $this->processor    = $processor;
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
        $this->setAdminNotices();
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
        add_action('admin_post_imo_form_response', array($this->processor, 'process'));
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
     * Define a custom flash message.
     *
     * @return void
     */
    protected function setAdminNotices(): void
    {
        add_action(
            'admin_notices',
            function () {
                $flash = get_transient('imo_flash_message');
                if ($flash) {
                    printf(
                        '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
                        esc_attr($flash['type']),
                        esc_html($flash['message'])
                    );
                    delete_transient('imo_flash_message');
                }
            }
        );
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
