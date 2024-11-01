<?php
/**
 * Plugin Name: Vii Related Posts
 * Plugin URI:
 * Description: Reduce bounce rates by display related (same category, same tag, specify tag), lasted, popular (comment count) or random posts to your site in the simple and fasted way. Support links, thumbnails, excerpts... and auto-exclude duplicates.
 * Version: 1.0
 * Author: ViiStudio
 * Author URI:
 * License: GPLv2 or later
 */

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Directly access denied!';
	exit;
}

register_uninstall_hook( __FILE__, array( 'Vii_Related_Posts', 'uninstall' ) );

$vii_related_post = new Vii_Related_Posts();
$vii_related_post->init();

class Vii_Related_Posts {
	public static $inused = array();

	/**
	 * Remove all options on uninstall
	 */
	public static function uninstall() {
		delete_option( 'widget_vii_related_posts' );
	}

	/**
	 * Init Plugin when WordPress Initialises.
	 */
	public function init() {
		add_action( 'widgets_init', array( $this, 'widgets' ) );
	}

	public function widgets() {
		include_once 'inc/vii-related-posts-widget.php';
		register_widget( 'Vii_Related_Posts_Widget' );
	}
}