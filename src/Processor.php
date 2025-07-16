<?php

/**
 * SettingsProcessor
 *
 * Precess settings.
 *
 * @package Image_Optimizer
 */

namespace ImageOptimizer;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * SettingsProcessor
 *
 * Processes settings.
 */
class Processor
{
    protected const SETTINGS_PAGE_URL = 'admin.php?page=renderImoSettings';
    protected Converter $converter;

    public function __construct(Converter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * Main method for processing settings.
     *
     * We check the nonce here.
     *
     * @return void
     */
    public function process(): void
    {
        $invalid_message = esc_html__('Invalid nonce specified.', 'imo');

        if (! isset($_POST['imo_nonce'])) {
            $this->imoWpDie($invalid_message, self::SETTINGS_PAGE_URL);
        }

        $nonce = sanitize_text_field(wp_unslash($_POST['imo_nonce']));

        if (wp_verify_nonce($nonce, 'imo_nonce')) {
            $this->converter->convert();
            $this->setFlashMessage('success', esc_html__('Conversion done.', 'imo'));
            $this->redirect();
        }

        $this->imoWpDie($invalid_message, self::SETTINGS_PAGE_URL);
    }

    /**
     * Custom wp_die. Displaying an error message with a back link.
     *
     * @param string $message
     * @param string $back_link
     * @return void
     */
    protected function imoWpDie(string $message, string $back_link): void
    {
        wp_die(
            esc_html($message),
            esc_html__('Error', 'imo'),
            array(
                'response'  => 500,
                'back_link' => esc_html($back_link),
            )
        );
    }

    /**
     * Set a flash message.
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    protected function setFlashMessage(string $type, string $message): void
    {
        set_transient(
            'imo_flash_message',
            array(
                'type'    => $type,
                'message' => $message,
            ),
            0
        );
    }

    /**
     * Redirect to the settings page.
     *
     * @return void
     */
    protected function redirect(): void
    {
        wp_safe_redirect(
            esc_url_raw(
                admin_url(
                    'admin.php?page=renderImoSettings'
                )
            )
        );
        exit;
    }
}
