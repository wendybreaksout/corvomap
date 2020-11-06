<?php
/**
 * Class: CrowdMap Page Manager
 *
 * This class creates pages that are used to for the default display of CrowdMap objects
 *
 * The functionality provided by this class is disabled for this plugin. To enable it, this
 * code must be placed under the calls to the CrowdMap_Settings class:
 *
 * $cmp_page_manager = new CrowdMap_Page_Manager();
   $this->loader->add_action('init', $cmp_page_manager, 'create_maps_listing_page');
   $this->loader->add_action('init', $cmp_page_manager, 'create_map_detail_page');
   $this->loader->add_action('init', $cmp_page_manager, 'add_rewrite_tags');
   $this->loader->add_action('init', $cmp_page_manager, 'add_rewrite_rules');
   $this->loader->add_filter('the_content', $cmp_page_manager, 'display_maps_list_page');
   $this->loader->add_filter('the_content', $cmp_page_manager, 'display_map_detail_page');
   $this->loader->add_filter('query_vars', $cmp_page_manager, 'add_query_vars');
 *
 *
 */

class CrowdMap_View_Manager {




	/**
	 *
	 * Registered as a 'the_content' filter. Returns the map listing shortcode output
	 * if the page is the maps listing page.
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function display_maps_list_page( $content ) {
		global $post;
		if ( $post->post_type == 'maps' ) {
			return do_shortcode('[map_listing]');
		}
		else {
			return $content;
		}

	}


	/**
	 * Registered as a 'the_content' filter. Returns the map detail shortcode output
	 * if the page is a maps detail page.
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function display_maps_post( $content ) {

		global $post;


		if ( $post->post_type == 'maps' ) {
			$content = do_shortcode( '[corvomap id="' . $post->ID . '" ]' );
		}

		return $content;

	}




}