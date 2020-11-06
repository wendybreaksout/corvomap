<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    CrowdMap
 * @subpackage CrowdMap/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    CrowdMap
 * @subpackage CrowdMap/public
 * @author     Wes Kempfer <wkempferjr@tnotw.com>
 */
class CrowdMap_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $crowdmap    The ID of this plugin.
	 */
	private $crowdmap;
	
	/**
	 * Handle for crowdmap public javascript.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $crowdmap_public_js_handle.
	 */
	private $crowdmap_public_js_handle = 'crowdmap-public-js';

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $crowdmap       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $crowdmap, $version ) {

		$this->crowdmap = $crowdmap;
		$this->version = $version;

	}
	
	

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in CrowdMap_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The CrowdMap_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		global $post;

		wp_enqueue_style( $this->crowdmap, plugin_dir_url( __FILE__ ) . 'css/crowdmap-public.css', array(), $this->version, 'all' );

		$post_type = get_post_type();
		if ( $post_type == "maps" ||  $this->has_cmp_shortcodes() ) {
		 	// wp_enqueue_style( 'cmp-slick', plugin_dir_url( __FILE__ ) . 'lib/slick/slick.css', array(), $this->version, 'all' );
		 	// wp_enqueue_style( 'slider-style', plugin_dir_url( __FILE__ ) . 'css/slider-style.css', array(), $this->version, 'all' );
			// wp_enqueue_style( 'cmp-foundation', plugin_dir_url( __FILE__ ) . 'css/foundation.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'cmp-image-picker', plugin_dir_url( __FILE__ ) . 'css/image-picker.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'cmp-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css', array(), $this->version, 'all' );

			wp_enqueue_style( 'cmp-map-icon-style', plugin_dir_url( __FILE__ ) . 'map-icons/css/map-icons.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'cmp-bootstrap', plugin_dir_url( __FILE__ ) . 'bootstrap/css/bootstrap.min.css', array(), $this->version, 'all' );



		}
		
		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in CrowdMap_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The CrowdMap_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */


		global $post ;

		$post_type = get_post_type();




		// Enqueue foundation and slider for map detail pages
		if ( $post_type == "maps" || $this->has_cmp_shortcodes() ) {

			$this->queue_google_maps();
			wp_enqueue_script( 'cmp-slick', plugin_dir_url( __FILE__ ) . 'lib/slick/slick.min.js', array( 'jquery' ), $this->version, true );

			wp_enqueue_script( 'cmp-image-picker', plugin_dir_url( __FILE__ ) . 'js/image-picker.js', array('jquery'), $this->version, true );
			wp_enqueue_script( 'cmp-map-icons', plugin_dir_url( __FILE__ ) . 'map-icons/js/map-icons.js', array('jquery', 'google-maps'), $this->version, true );

			wp_enqueue_script( 'cmp-marker-clusterer', plugin_dir_url( __FILE__ ) . 'js/markerclusterer.min.js', array('jquery'), $this->version, true );
			wp_enqueue_script( 'cmp-spin', plugin_dir_url( __FILE__ ) . 'js/spin.min.js', array('jquery'), $this->version, true );
			wp_enqueue_script( 'cmp-bootstrap', plugin_dir_url( __FILE__ ) . 'bootstrap/js/bootstrap.min.js', array('jquery'), $this->version, true );




			wp_enqueue_script( $this->crowdmap_public_js_handle, plugin_dir_url( __FILE__ ) . 'js/crowdmap-public.js', array( 'jquery', 'google-maps' ), $this->version, true );

		}

	}
	
	
	/**
	 * Runs wp_localize_script in order to pass localized strings to javascripts. 
	 */
	public function localize_scripts () {


		$maps_post_id = get_the_ID();


		$wp_js_info = array('site_url' => __(site_url()));

		$proposal_types = get_post_meta($maps_post_id, 'meta_proposal_type_list', true);

		wp_localize_script( $this->crowdmap_public_js_handle , 'objectl10n', array(
			'wpsiteinfo' => $wp_js_info,
			'postID' => $maps_post_id,
			'sliderOn' => false,
			'get_multiple_proposals_error' => __('Error retrieving multiple proposals.',  GUESTABA_CMP_TEXTDOMAIN ),
			'get_activity_error' =>  __('Error retrieving activity.',  GUESTABA_CMP_TEXTDOMAIN ),
			'post_comment_error' =>  __('Error posting comment.',  GUESTABA_CMP_TEXTDOMAIN ),
			'post_support_error' =>  __('Error posting support.',  GUESTABA_CMP_TEXTDOMAIN ),
			'get_proposal_error' =>  __('Error retrieving proposal.',  GUESTABA_CMP_TEXTDOMAIN ),
			'publish_proposal_error' =>  __('Error publishing proposal.',  GUESTABA_CMP_TEXTDOMAIN ),
			'since_string_before' => __('off', GUESTABA_CMP_TEXTDOMAIN),
			'since_string_after' => __('ago', GUESTABA_CMP_TEXTDOMAIN),
			'hours' => __('hours', GUESTABA_CMP_TEXTDOMAIN),
			'minutes' => __('minutes', GUESTABA_CMP_TEXTDOMAIN),
			'days' => __('days', GUESTABA_CMP_TEXTDOMAIN),
			'over_a_year' => __('Over at year ago', GUESTABA_CMP_TEXTDOMAIN),
			'location_map_error' => __('Error retrieving location map options.',  GUESTABA_CMP_TEXTDOMAIN ),
			'server_error' => __('Server error:', GUESTABA_CMP_TEXTDOMAIN ),
			'supported_label' => __('Supported', GUESTABA_CMP_TEXTDOMAIN ),
			'not_logged_in_msg' => __('You must be logged in to post.', GUESTABA_CMP_TEXTDOMAIN),
			'geolocation_not_supported' => __('Geolocation is not supported by this browser.', GUESTABA_CMP_TEXTDOMAIN),
			'user_denied_geolocation_request' =>  __('Location services disabled for this website.', GUESTABA_CMP_TEXTDOMAIN),
			'location_is_unavailable' =>  __('Location information is unavailable.', GUESTABA_CMP_TEXTDOMAIN),
			'location_request_timeout' =>  __('The request to get user location timed out.', GUESTABA_CMP_TEXTDOMAIN),
			'unknown_error_occurred' =>  __('An unknown error occurred.', GUESTABA_CMP_TEXTDOMAIN),
			'all_fields_required' => __('All fields are required', GUESTABA_CMP_TEXTDOMAIN),
			'activity_refresh_interval' => 30000,
			'default_map_icon' => plugin_dir_url( __FILE__ ) . 'img/map-icons/footprint.png',
			'proposal_types' => $proposal_types,
			'proposal_info_template' => CrowdMap_Client_Template_Manager::get_info_window_template(),
			'activity_list_template' => CrowdMap_Client_Template_Manager::get_activity_list_template(),
			'activity_item_template' => CrowdMap_Client_Template_Manager::get_activity_item_template(),
			'proposal_form_status_template' => CrowdMap_Client_Template_Manager::get_proposal_form_status_template(),
			'is_user_logged_in' => is_user_logged_in(),
			'mobile_breakpoint' => 667,
			'display_mobile_format' => true
		));
	}
	
	
	/**
	 * Register plugin widget areas.
	 * 
	 * @since 1.0.0
	 */
	public function register_widget_areas () {

		register_sidebar( array(
			'name' => 'Map First Widget Area',
			'id' => 'map_first_widget_area',
			'before_widget' => '<div class="cmp_widget">',
			'after_widget' => '</div>',
			'before_title' => '<h2 class="cmp_widget_title">',
			'after_title' => '</h2>',
		) );

		register_sidebar( array(
	        'name' => 'Map Second Widget Area',
	        'id' => 'map_second_widget_area',
	        'before_widget' => '<div class="cmp_widget">',
	        'after_widget' => '</div>',
	        'before_title' => '<h2 class="cmp_widget_title">',
	        'after_title' => '</h2>',
	    ) );
	
	
	    register_sidebar( array(
	        'name' => 'Map Third Widget Area',
	        'id' => 'map_third_widget_area',
	        'before_widget' => '<div class="cmp_widget">',
	        'after_widget' => '</div>',
	        'before_title' => '<h2 class="cmp_widget_title">',
	        'after_title' => '</h2>',
	    ) );
	    

	}

	private function has_cmp_shortcodes() {

		global $post;

		$post_type = get_post_type();

		$has_shortcodes = false;
		if ( $post_type == "post"|| $post_type == "page" ) {

			foreach( CrowdMap_Shortcodes::get_shortcodes() as $shortcode ) {
				if ( has_shortcode( $post->post_content, $shortcode ) ) {
					$has_shortcodes = true;
					break;
				}
			}
		}
		return $has_shortcodes;
	}

	private function get_slider_shortcode_IDs() {

		global $post;
		$pattern = get_shortcode_regex();

		preg_match_all('/'.$pattern.'/s', $post->post_content, $matches );

		$ids = array();
		foreach ( $matches[0] as $match ) {
			if ( preg_match('/map_images/', $match )) {
				if ( preg_match( '/id=\"?[0-9]+\"?/',$match, $id_attr_match ) ) {
					if ( preg_match('/[0-9]+/', $id_attr_match[0], $id_match ) ) {
						$ids[] = $id_match[0];
					}
				}
			}
		}

		return $ids;
	}
	/*
	 * Support function for location_map, queues google api js.
	 *
	 * @since 1.0.4
	 */

	private function queue_google_maps() {

		$options = get_option( GUESTABA_CMP_OPTIONS_NAME );
		$api_key = $options['cmp_google_maps_api_key'];

		// $google_map_url = '//maps.googleapis.com/maps/api/js?v=3&libraries=visualization&key=' . $api_key . '&callback=initCrowdMap';
		$google_map_url = '//maps.googleapis.com/maps/api/js?v=3&libraries=visualization&key=' . $api_key ;

		wp_enqueue_script(
			'google-maps',
			$google_map_url,
			array(),
			'1.0',
			true
		);

	}


	/**
	 *
	 * Function: get_geocoding
	 *
	 * @return array returns geo coding json string for address in plugin settings.
	 */
	private static function get_geocoding() {

		$option = get_option( GUESTABA_CMP_GEO_OPTIONS_NAME );
		$geocode_json = $option['geocode'] ;

		$geocode_array = json_decode( $geocode_json );

		return $geocode_array->results[0]->geometry->location ;

	}



      
}