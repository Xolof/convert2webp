<?php

/**
 * Class ResultsFetcher
 *
 * Fetches votation results from the database.
 *
 * @package Image Optimizer
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

    public function getImages(): array
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

                if (array_key_exists('extension', pathinfo($path))) {
                    if (in_array(pathinfo($path)['extension'], $extensions)) {
                        $this->result[] = $path;
                    }
                };
            };
        }
    }
}
