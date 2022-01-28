<?php
get_header();
?>
	<?php 

	$home_url = home_url();
	$security = wp_create_nonce( 'wp_rest' );
	$webinar_id = get_queried_object_id();
	// TODO: dodać docelowe wersjonowanie
	$css_url = WEBINAR_PLUGIN_URL . 'components/webinar/public/build/bundle.css?version=' . time();
	$js_url = WEBINAR_PLUGIN_URL . 'components/webinar/public/build/bundle.js?version=' . time();
	?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<?php
			if ( have_posts() ) :	while ( have_posts() ) : the_post(); ?>
				<link rel='stylesheet' href='https://fonts.googleapis.com/icon?family=Material+Icons'>
				<link rel='stylesheet' href='<?= $css_url ?>'>
				<script>
					const webinarData = {
						homeUrl: '<?= $home_url; ?>',
						security: '<?= $security; ?>',
						webinarId: <?= $webinar_id; ?>
					}
				</script>
				<script src='https://cdn.ckeditor.com/ckeditor5/23.1.0/classic/ckeditor.js'></script>
				<div class='bcim-webinar'><div>
				<script src='<?= $js_url ?>'></script>
			
			<?php endwhile; endif;?>

		</main>
	</div>

<?php
get_footer();
