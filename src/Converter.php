<?php

/**
 * Class Converter
 *
 * Converts images to webp.
 *
 * @package Image Optimizer
 */

namespace ImageOptimizer;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Converter
 *
 * Converts images to webp.
 *
 */
class Converter
{
    protected ResultsFetcher $results_fetcher;
    protected ImoLogger $logger;
    protected Db $db;

    public function __construct(
        ResultsFetcher $results_fetcher,
        ImoLogger $logger,
        Db $db
    ) {
        $this->results_fetcher = $results_fetcher;
        $this->logger = $logger;
        $this->db = $db;
    }

    public function convert(): void
    {
        $images = $this->results_fetcher->getImages();
        $imageCount = count($images);

        foreach ($images as $index => $imageUrl) {
            $this->logger->log("Converting image " . $index + 1 . " out of $imageCount.");
            $this->logger->log($imageUrl);
            $this->convertImage($imageUrl);
        }
    }

    protected function convertImage(string $imageUrl): void
    {
        // TODO:
        // Write to the log here.
        // New log file for each run.
        // After the job has finished we delete the old log files.
        // We can keep the latest log files.

        if (pathinfo($imageUrl)['extension'] === "jpg") {
            $img = imagecreatefromjpeg($imageUrl);
            $imageUrlAfter = str_replace(".jpg", ".webp", $imageUrl);
        } elseif (pathinfo($imageUrl)['extension'] === "jpeg") {
            $img = imagecreatefromjpeg($imageUrl);
            $imageUrlAfter = str_replace(".jpeg", ".webp", $imageUrl);
        } elseif (pathinfo($imageUrl)['extension'] === "png") {
            $img = imagecreatefrompng($imageUrl);
            $imageUrlAfter = str_replace(".png", ".webp", $imageUrl);
        } else {
            throw new \Exception("The image format of this file can not be handled: $imageUrl");
        };

        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);
        imagewebp($img, $imageUrlAfter, 100);
        imagedestroy($img);

        $expl = explode("/", $imageUrl);
        $filenameBefore = htmlspecialchars(end($expl));
        $filenameBefore = str_replace(' ', '', $filenameBefore);

        $expl = explode("/", $imageUrlAfter);
        $filenameAfter = htmlspecialchars(end($expl));
        $filenameAfter = str_replace(' ', '', $filenameAfter);

        $attachmentUrl = str_replace($_SERVER['DOCUMENT_ROOT'], home_url(), $imageUrl);

        $attachment_id = attachment_url_to_postid($attachmentUrl);

        if ($attachment_id) {
            $this->logger->log("Generating attachment data.");
            $attach_data = wp_generate_attachment_metadata($attachment_id, $imageUrlAfter);
            wp_update_attachment_metadata($attachment_id, $attach_data);
        }

        $this->db->searchReplace($filenameBefore, $filenameAfter);

        unlink($imageUrl);
    }
}
