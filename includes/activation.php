<?php
function webinar_add_bcim_questions_table() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'bcim_webinar_questions';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		question_id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id mediumint(9) NOT NULL,
        webinar_id mediumint(9) NOT NULL,
        question_content LONGTEXT NOT NULL,
		question_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        question_reply LONGTEXT NOT NULL,
        question_reply_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		PRIMARY KEY  (question_id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}