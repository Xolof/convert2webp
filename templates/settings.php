<?php

/**
 * Image Optimization page
 *
 * @package Image_Optimizer
 */

if (! defined('ABSPATH')) {
    exit;
}

?>

<main class="imoMain">
    <h1><?php echo esc_html__('Image Optimizer', 'imo'); ?></h1>

    <?php if (current_user_can('manage_options')) : ?>
        <?php $imo_nonce = wp_create_nonce('imo_nonce'); ?>

        <?php $imageCount = count($images) ?>

        <?php if ($imageCount) : ?>
            <p class="imagesToBeConvertedInfo">
                Found <?= $imageCount ?> Jpg and Png images which can be converted to Webp.
            </p>
            <button id="convert-button" class="button button-primary">Convert</button>
            <div class="resultsDiv">
                <h3>Images to be converted</h3>
                <?php foreach ($images as $image) : ?>
                    <p><?= esc_html($image) ?></p>
                <?php endforeach; ?>
            </div>
            <span class="loader"></span>
            <div class="logDiv"></div>
        <?php else : ?>
            <p class="imagesToBeConvertedInfo">There are no Jpg or Png images to be converted.</p>
        <?php endif; ?>

    <?php else : ?>
        <p><?php echo esc_html__('You are not authorized to perform this operation.', 'imo'); ?></p>
    <?php endif; ?>
</main>