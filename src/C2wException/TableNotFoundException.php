<?php

/**
 * Class TableNotFoundException
 *
 * @package Convert2Webp
 */

namespace Convert2Webp\C2wException;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * TableNotFoundException
 *
 * An exception to be thrown when a database table does not exist.
 */
class TableNotFoundException extends \Exception
{
}
