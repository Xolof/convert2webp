<?php

/**
 * Admin page for Convert 2 Webp.
 *
 * @package Convert2Webp
 */

if (! defined('ABSPATH')) {
    exit;
}

?>

<main class="c2wMain">
    <h1><?php echo esc_html__('Convert 2 Webp', 'c2w'); ?></h1>

    <?php if (current_user_can('manage_options')) : ?>
        <?php $c2w_nonce = wp_create_nonce('c2w_nonce'); ?>

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
            <span class="loader">
                <span class="bar"></span>
            </span>
            <div class="logDiv"></div>
        <?php else : ?>
            <p class="imagesToBeConvertedInfo">There are no Jpg or Png images to be converted.</p>
        <?php endif; ?>

    <?php else : ?>
        <p><?php echo esc_html__('You are not authorized to perform this operation.', 'c2w'); ?></p>
    <?php endif; ?>
</main>