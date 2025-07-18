<?php

/**
 * Plugin Name:     Convert2Webp
 * Plugin URI:      https://github.com/xolof/convert2webp
 * Description:     A minimalist plugin for converting JPEG and PNG images to WEBP. One click, no bloat.
 * Author:          Olof Johansson
 * Author URI:      https://oljo.xyz
 * Text Domain:     c2w
 * Domain Path:     /languages
 * Version:         0.0.1
 *
 * @package         Convert2Webp
 */

if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/debug/functions.php';

use Convert2Webp\Convert2Webp;
use Convert2Webp\Db;
use Convert2Webp\ResultsFetcher;
use Convert2Webp\MenuManager;
use Convert2Webp\Logger;
use Convert2Webp\Converter;

$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
define('CONVERT2WEBP_VERSION', $plugin_data['Version']);

$logger                = new Logger();
$db                    = new Db();
$results_fetcher       = new ResultsFetcher();
$converter             = new Converter($results_fetcher, $logger, $db);
$menu_manager          = new MenuManager($results_fetcher);

$c2w = new Convert2Webp(
    $converter,
    $menu_manager
);

$c2w->init();
