<?php

/**
 * Class ImageFetcher
 *
 * Fetches image filenames.
 *
 * @package Convert2Webp
 */

namespace Convert2Webp;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * ImageFetcher
 *
 * Fetches image filenames.
 */
class ImageFetcher
{
    protected array $result;

    public function __construct()
    {
        $this->result = [];
    }

    /**
     * Get images which can be converted in upload directory.
     */
    public function getImages(): array
    {
        $uploadsDir = wp_get_upload_dir()['basedir'];
        $this->checkDirectory($uploadsDir);
        return $this->result;
    }

    /**
     * Scan directory for files which can be converted
     * and add them to results.
     * Call itself on directories.
     */
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
