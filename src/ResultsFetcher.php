<?php

/**
 * Class ResultsFetcher
 *
 * Fetches votation results from the database.
 *
 * @package Forminator Voting System
 */

namespace ImageOptimizer;

if (! defined('ABSPATH')) {
	exit;
}

/**
 * ResultsFetcher
 *
 * Fetches results.
 */
class ResultsFetcher
{
	protected array $result;

	public function __construct()
	{
		$this->result = [];
	}

	public function get_images(): array
	{	
		$uploadsDir = wp_get_upload_dir()['basedir'];
		$this->checkDirectory($uploadsDir);
		return $this->result;
	}

	protected function checkDirectory(string $dir): void
	{
		$fileNames = array_slice(scandir($dir), 2);

		foreach ($fileNames as $fileName) {
			$path = $dir . "/" . $fileName;

			if (is_dir($path)) {
				$this->checkDirectory($path);
			} else {
				$extensions = ["jpg", "jpeg", "png"];

				if (in_array(pathinfo($path)['extension'], $extensions)) {
					$this->result[] = $path;
				}
			};
		}
	}
}
