<?php

/**
 * Class TableNotFoundException
 *
 * @package Image Optimizer
 */

namespace ImageOptimizer\ImoException;

if (! defined('ABSPATH')) {
	exit;
}

/**
 * TableNotFoundException
 *
 * An exception to be thrown when a database table does not exist.
 */
class TableNotFoundException extends \Exception {}
