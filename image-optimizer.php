<?php

/**
 * Plugin Name:     Image Optimizer
 * Plugin URI:      https://github.com/xolof/forminator-voting-system
 * Description:     Simple plugin for optimizing your images.
 * Author:          Olof Johansson
 * Author URI:      https://oljo.xyz
 * Text Domain:     imo
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Image_Optimizer
 */

if (! defined('ABSPATH')) {
	exit;
}

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/debug/functions.php';

use ImageOptimizer\Optimizer;
use ImageOptimizer\Db;
use ImageOptimizer\Processor;
use ImageOptimizer\ResultsFetcher;
use ImageOptimizer\MenuManager;
use ImageOptimizer\ImoLogger;
use ImageOptimizer\Converter;

$logger 			   = new ImoLogger();
$db					   = new Db();
$results_fetcher       = new ResultsFetcher();
$converter 			   = new Converter($results_fetcher, $logger, $db);
$processor    		   = new Processor($converter);
$menu_manager          = new MenuManager($results_fetcher);

$image_optimizer = new Optimizer(
	$processor,
	$menu_manager
);

$image_optimizer->init();
