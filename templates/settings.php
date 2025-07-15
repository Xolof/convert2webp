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

<h1><?php echo esc_html__('Image Optimizer', 'imo'); ?></h1>

<?php if (current_user_can('manage_options')) : ?>
	<?php $imo_nonce = wp_create_nonce('imo_nonce'); ?>

	<?php $imageCount = count($images) ?>
	<p>Found <?= $imageCount ?> Jpg and Png images which can be converted to Webp.</p>


	<!--
		TODO
		Make an AJAX endpoint for converting.
		Make an AJAX endpoint for showing the log.
		Print the log in real time.
	-->
	<?php if ($imageCount): ?>
		<div class="imo_form">
			<form
				action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
				method="POST"
				id="imo_form">
				<input type="hidden" name="action" value="imo_form_response" />
				<input type="hidden" name="imo_nonce" value="<?php echo esc_html($imo_nonce); ?>" />
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html__('Convert to Webp', 'imo'); ?>" />
			</form>
		</div>
	<?php endif; ?>

	<?php foreach ($images as $image): ?>
		<p><?= $image ?></p>
	<?php endforeach; ?>

<?php else : ?>
	<p><?php echo esc_html__('You are not authorized to perform this operation.', 'imo'); ?></p>
<?php endif; ?>