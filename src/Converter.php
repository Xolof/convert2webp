<?php

/**
 * Class Converter
 *
 * Converts images to webp.
 *
 * @package Convert2Webp
 */

namespace Convert2Webp;

if (! defined('ABSPATH')) {
    exit;
}

if (! function_exists('wp_generate_attachment_metadata')) {
    include(ABSPATH . 'wp-admin/includes/image.php');
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
    protected Logger $logger;
    protected Db $db;

    public function __construct(
        ResultsFetcher $results_fetcher,
        Logger $logger,
        Db $db
    ) {
        $this->results_fetcher = $results_fetcher;
        $this->logger = $logger;
        $this->db = $db;
    }

    /**
     * Iterate over the images and convert them.
     */
    public function convert(): void
    {
        // Disable time limit for the script.
        set_time_limit(0);
        $images = $this->results_fetcher->getImages();
        $imageCount = count($images);

        $this->logger->clear();
        $this->logger->log([
            "message" => "Starting conversion of images.",
            "type" => "c2wLogInfo"
        ]);

        foreach ($images as $index => $imageUrl) {
            $imageUrl = htmlentities($imageUrl, ENT_QUOTES, 'UTF-8');
            $currentImageNumber = $index + 1;
            $this->logger->log([
                "message" => "Converting image $currentImageNumber out of $imageCount: $imageUrl",
                "type" => "c2wLogInfo",
                "currentImageNumber" => $currentImageNumber,
                "imageCount" => $imageCount
            ]);
            try {
                $this->convertImage($imageUrl);
            } catch (\Exception $e) {
                error_log($e);
                $this->logger->log([
                    "message" => "Error when converting $imageUrl. See Wordpress error log for details.",
                    "type" => "c2wLogError"
                ]);
            }
            // Let PHP sleep to decrease server load.
            sleep(1);
        }
        $this->logger->log([
            "message" => "Conversion finished.",
            "type" => "c2wLogInfo"
        ]);
    }

    /**
     * Convert an image to WEBP.
     */
    protected function convertImage(string $imageUrl): void
    {
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
            $this->logger->log([
                "message" => "Generating attachment data.",
                "type" => "c2wLogInfo"
            ]);
            $attach_data = wp_generate_attachment_metadata($attachment_id, $imageUrlAfter);
            wp_update_attachment_metadata($attachment_id, $attach_data);
        }

        $this->db->searchReplace($filenameBefore, $filenameAfter);

        unlink($imageUrl);
    }
}
