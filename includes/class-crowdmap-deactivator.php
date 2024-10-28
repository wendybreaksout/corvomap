<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    CrowdMap
 * @subpackage CrowdMap/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation. 
 * It does not do anything at this time. Data removal is in the uninstall.php 
 * script. 
 *
 * @since      1.0.0
 * @package    CrowdMap
 * @subpackage CrowdMap/includes
 * @author     Wendy Emerson <wendybreaksout@gmail.com>
 */
class CrowdMap_Deactivator {

	public static function deactivate() {
		flush_rewrite_rules();
		self::remove_posts();
	}

	public static function uninstall() {

	}

	/**
	 * Remove posts.
	 *
	 * @since    1.0.0
	 */
	public static function remove_posts() {

		$settings = new CrowdMap_Settings();
		if ( $settings->get_remove_data_on_uninstall() == true ) {
			$settings->delete_options();

			// Remove posts
			$type = 'proposals';
			$args=array(
				'post_type' => $type,
				'post_status' => '',
				'posts_per_page' => -1,
				'caller_get_posts'=> 1
			);

			$post_query = null;
			$post_query = new WP_Query($args);
			if( $post_query->have_posts() ) {
				while ($post_query->have_posts()) : $post_query->the_post();
					$post_ID = get_the_ID();
					wp_delete_post( $post_ID, true );
				endwhile;
				wp_reset_query();  // Restore global post data stomped by the_post().

			}



			$type = 'maps';
			$args=array(
				'post_type' => $type,
				'post_status' => '',
				'posts_per_page' => -1,
				'caller_get_posts'=> 1
			);

			$post_query = null;
			$post_query = new WP_Query($args);
			if( $post_query->have_posts() ) {
				while ($post_query->have_posts()) : $post_query->the_post();
					$post_ID = get_the_ID();
					wp_delete_post( $post_ID, true );
				endwhile;
				wp_reset_query();  // Restore global post data stomped by the_post().

			}
		}


	}

}
