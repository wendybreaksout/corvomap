<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://guestaba.com
 * @since      1.0.0
 *
 * @package    CrowdMap
 * @subpackage CrowdMap/admin
 * @author     Wes Kempfer <wkempferjr@tnotw.com>
 */
class CrowdMap_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $crowdmap    The ID of this plugin.
	 */
	private $crowdmap;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/** Handle for crowdmap-admin javascript.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $crowdmap_admin_js_handle
	 */
	private $crowdmap_admin_js_handle = 'crowdmap-map-admin-js';
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $crowdmap       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $crowdmap, $version ) {

		$this->crowdmap = $crowdmap;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		wp_enqueue_style( $this->crowdmap, plugin_dir_url( __FILE__ ) . 'css/crowdmap-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'image-picker', plugin_dir_url( __FILE__ ) . 'css/image-picker.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'wp-color-picker' );


	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script( 'gst-character-counter', plugin_dir_url( __FILE__ ) . 'js/vendor/jquery.simplyCountable.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->crowdmap, plugin_dir_url( __FILE__ ) . 'js/crowdmap-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, false );
		wp_enqueue_script( 'image-picker-js', plugin_dir_url( __FILE__ ) . 'js/image-picker.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'cmp-spin', plugin_dir_url( __FILE__ ) . 'js/spin.min.js', array('jquery'), $this->version, true );

	}
	
	/**
	 * Runs wp_localize_script in order to pass localized strings to javascripts. 
	 * 
	 * @since    1.0.0
	 */
	public function localize_scripts () {
		
		$wp_js_info = array('site_url' => __(site_url()));
		
		wp_localize_script( $this->crowdmap , 'cmp_admin_objectl10n', array(
			'wpsiteinfo' => $wp_js_info,
			'get_proposal_error' => __('Error retrieving proposal.',  GUESTABA_CMP_TEXTDOMAIN ),
			'server_error' => __('Server error:', GUESTABA_CMP_TEXTDOMAIN ),
			'note_no_proposal_selected' => __('No proposal selected.', GUESTABA_CMP_TEXTDOMAIN),
			'proposal_table_template' => CrowdMap_Client_Template_Manager::get_proposal_table_template(),
			'proposal_table_heading_template' => CrowdMap_Client_Template_Manager::get_proposal_table_heading_template(),
			'proposal_table_row_template' => CrowdMap_Client_Template_Manager::get_proposal_table_row_template()

		));
	}

}
