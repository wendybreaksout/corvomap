<?php 

/**
 * This class defines and maintains access to the plugin 
 * settings. 
 * 
 * @link       http://guestaba.com
 * @since      1.0.0
 * @package    CrowdMap
 * @subpackage CrowdMap/includes
 * @author     Wes Kempfer <wkempferjr@tnotw.com>
 */
class CrowdMap_Settings {
	
	
	/*
	 * Sets the name of plugin option.
	 */
	private $options_name = GUESTABA_CMP_OPTIONS_NAME;
	
	/*
	 * Default values for plugin options are defined here. 
	 * These values are recorded in wp_option at activation time. 
	 * 
	 */
	private $default_use_widget_area = false;
	private $default_remove_data_on_uninstall = false;
	private $version = GUESTABA_CORVOMAP_VERSION_NUM ;
	
	/**
	 * Constructor
	 * 
	 * @since 1.0.0
	 */
	public function __construct() {

	}
	
	
	/*
	 * Get the plugin option name. 
	 * 
	 * @return string plugin option name.
	 */
	public function get_options_name() {
		return $this->options_name;
	}
	
	
	/*
	 * This function is called at activation time and by the constructor. It records
	 * the plugin settings default values in the wp_options table. 
	 * If the plugin options already exist in the database, they 
	 * are not overwritten. 
	 * 
	 * @since 1.0.0
	 */
	public function add_option_defaults() {
		
		if ( current_user_can('activate_plugins') ) {	
			$options = array();
			$options['cmp_remove_data_on_uninstall'] = $this->default_remove_data_on_uninstall;
			$options['version'] = $this->version ;
			$options['cmp_street_address_1'] = '';
			$options['cmp_street_address_2'] = '';
			$options['cmp_city'] = '';
			$options['cmp_state'] = '';
			$options['cmp_country'] = '';
			$options['cmp_postal_code'] = '';
			$options['cmp_google_maps_api_key'] = '';
			$options['cmp_google_geocode_api_key'] = '';
			$options['cmp_google_maps_default_zoom'] = '';
			$options['cmp_mobile_view_on'] = '';
			$options['cmp_mobile_view_threshold'];


			add_option( $this->options_name, $options );
		}
		
	}




	/*
	 * This function was intended to be called to delete the 
	 * options from the database. 
	 * 
	 * @todo Can this delete_options() be removed. 
	 * @since 1.0.0
	 */
	
	public function delete_options() {
		if ( current_user_can('delete_plugins') ) {
			delete_option($this->options_name );			
		}
	}
	


	


	
	/*
	 * Return "remove data on uninstall" flag. If true, all
	 * data and settings associated with the plugin are to be delete.
	 * 
	 * @since 1.0.0
	 * 
	 * @param none
	 * @return boolean remove_plugin_data_on_uninstall
	 */
	public function get_remove_data_on_uninstall() {
		$option = get_option( $this->options_name);
		return $option['cmp_remove_data_on_uninstall'];
	}






	/*
	 * Return google maps api key
	 *
	 * @since 1.0.4
	 * @param none
	 * @return string google maps api key
	 */
	public function get_google_maps_api_key() {
		$option = get_option( $this->options_name);
		return $option['cmp_google_maps_api_key'];
	}



	/*
	 * Return google geocode api key
	 *
	 * @since 1.0.4
	 * @param none
	 * @return string google geocode api key
	 */
	public function get_google_geocode_api_key() {
		$option = get_option( $this->options_name);
		return $option['cmp_google_geocode_api_key'];
	}

	/*
	 * Return CMP mobile view on option.
	 *
	 * @since 1.0.0
	 * @param none
	 * @return true if mobile view is enabled, false if not.
	 */
	public function get_cmp_mobile_view_on() {
		$option = get_option( $this->options_name);
		return $option['cmp_mobile_view_on'];
	}

	/*
	 * Return CMP mobile view on option.
	 *
	 * @since 1.0.0
	 * @param none
	 * @return true if mobile view is enabled, false if not.
	 */
	public function get_cmp_mobile_view_threshold() {
		$option = get_option( $this->options_name);
		return $option['cmp_mobile_view_threshold'];
	}


	/*
	 * This method defines the plugin setting page. 
	 * 
	 * @since 1.0.0
	 * 
	 * @param none
	 * @return void
	 */
	public function settings_init(  ) {

		register_setting( 'cmp-settings-group', $this->options_name, array( $this, 'sanitize') );
		
		add_settings_section(
			'cmp-settings-general-section',
			__( 'CorvoMap General Settings', GUESTABA_CMP_TEXTDOMAIN ),
			array($this, 'cmp_settings_general_info'),
			'cmp-settings-page'
		);		
		
		add_settings_field( 
			'cmp_remove_data_at_uninstall',
			__( 'Remove plugin posts, settings, and other data on deactivation.', GUESTABA_CMP_TEXTDOMAIN ),
			array($this, 'cmp_remove_data_render'),
			'cmp-settings-page',
			'cmp-settings-general-section'
		);



		// settings field google maps api key
		add_settings_field(
			'cmp_google_maps_api_key',
			__( 'Google Maps API Key', GUESTABA_CMP_TEXTDOMAIN ),
			array($this, 'cmp_google_maps_api_key_render'),
			'cmp-settings-page',
			'cmp-settings-general-section'
		);

		// settings field google maps api key
		add_settings_field(
			'cmp_google_geocode_api_key',
			__( 'Google Geocode API Key', GUESTABA_CMP_TEXTDOMAIN ),
			array($this, 'cmp_google_geocode_api_key_render'),
			'cmp-settings-page',
			'cmp-settings-general-section'
		);




	}
	
	/*
	 * Calls add_options_page to register the page and menu item.
	 * 
	 * @since 1.0.0
	 * 
	 * @param none
	 * @return integer map_desc_excerpt_len
	 */
	public function add_cmp_options_page( ) {

		// Add the top-level admin menu
		$page_title = 'CorvoMap Plugin Setings';
		$menu_title = 'CorvoMap';
		$capability = 'manage_options';
		$menu_slug = 'crowdmap-settings';
		$function = 'settings_page';
		add_options_page($page_title, $menu_title, $capability, $menu_slug, array($this, $function)) ;


	}
	
	/*
	 * Defines and displays the plugin settings page.
	 * @since 1.0.0
	 * 
	 * @param none
	 * @return none
	 */
	public function settings_page(  ) {

		$this->add_option_defaults();
	
		?>
		<div class="wrap">
		<form action='options.php' method='post'>
			
			<h2>CorvoMap Settings</h2>
			<div id="cmp-settings-container">
				<?php

				settings_fields( 'cmp-settings-group' );
				do_settings_sections( 'cmp-settings-page' );
				submit_button();
				?>
			</div>
			<div id="cmp-settings-info-container">
				<h3>CorvoMap from Guestaba</h3>
				<p><em> Version: <?php echo $this->version ?></em></p>
				<h3>Help Improve this Plugin</h3>
					<p>Send us your ideas, feature requests and...donations :)</p>
					<p><a id="cmp-setting-contact" href="https://guestaba.com/contact" target="_blank">Contact us</a></p>
					<p><a id="cmp-setting-help" href="https://support.guestaba.com/documentation" target="_blank">CorvoMap Documentation</a></p>
					<p><a id="cmp-setting-help" href="https://support.guestaba.com" target="_blank">Support</a></p>
				
			<div id="cmp-donate">
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="FUWXV5MTNZWW4">
						<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>
				</div>
			</div>

			
		</form>
		</div>
		<?php

	}

	
	
	/*
	 * Render the remove data on unsinstal checkbox field. 
	 * @since 1.0.0
	 */	
	public function cmp_remove_data_render(  ) {
	
		$options = get_option( $this->options_name );
		?>
		<input id="remove_cmp_data_input" type="checkbox" name="guestaba_cmp_settings[cmp_remove_data_on_uninstall]" <?php checked( $options['cmp_remove_data_on_uninstall'], 1 ); ?> value='1'>
		<br><label for="remove_cmp_data_input"><em>Leave this unchecked unless you really want to remove the posts you have created using this plugin.</em></label>
		<?php
	
	}





	/*
	 *
	 * Render the google maps api key field
	 * @since 1.0.4
	 *
    */
	public function cmp_google_maps_api_key_render(  ) {

		$options = get_option( $this->options_name );
		?>
		<input type="text" size="50" name="guestaba_cmp_settings[cmp_google_maps_api_key]"
		       value="<?php echo $options['cmp_google_maps_api_key']; ?>">
		<?php
	}

	/*
	 *
	 * Render the google maps api key field
	 * @since 1.0.4
	 *
    */
	public function cmp_google_geocode_api_key_render(  ) {

		$options = get_option( $this->options_name );
		?>
		<input type="text" size="50" name="guestaba_cmp_settings[cmp_google_geocode_api_key]"
		       value="<?php echo $options['cmp_google_geocode_api_key']; ?>">
		<?php
	}



	/*
	 * Sanitize user input before passing values on to update options.
	 * @since 1.0.0
	 */	
	public function sanitize( $input ) {
		
		$new_input = array();
		
		if( isset( $input['cmp_remove_data_on_uninstall'] ) ) {
        	 $new_input['cmp_remove_data_on_uninstall'] = sanitize_text_field( $input['cmp_remove_data_on_uninstall'] );
        }
        else {
        	// set to default 
        	$new_input['cmp_remove_data_on_uninstall'] = false ;
        }


		if( isset( $input['cmp_street_address_1'] ) )
			$new_input['cmp_street_address_1'] = sanitize_text_field( $input['cmp_street_address_1'] );

		if( isset( $input['cmp_street_address_2'] ) )
			$new_input['cmp_street_address_2'] = sanitize_text_field( $input['cmp_street_address_2'] );

		if( isset( $input['cmp_city'] ) )
			$new_input['cmp_city'] = sanitize_text_field( $input['cmp_city'] );

		if( isset( $input['cmp_state'] ) )
			$new_input['cmp_state'] = sanitize_text_field( $input['cmp_state'] );

		if( isset( $input['cmp_country'] ) )
			$new_input['cmp_country'] = sanitize_text_field( $input['cmp_country'] );

		if( isset( $input['cmp_postal_code'] ) )
			$new_input['cmp_postal_code'] = sanitize_text_field( $input['cmp_postal_code'] );

		if( isset( $input['cmp_google_maps_api_key'] ) )
			$new_input['cmp_google_maps_api_key'] = sanitize_text_field( $input['cmp_google_maps_api_key'] );

		if( isset( $input['cmp_google_geocode_api_key'] ) )
			$new_input['cmp_google_geocode_api_key'] = sanitize_text_field( $input['cmp_google_geocode_api_key'] );

		if( isset( $input['cmp_google_maps_default_zoom'] ) )
			$new_input['cmp_google_maps_default_zoom'] = intval( $input['cmp_google_maps_default_zoom'] );


		return $new_input ;
	}
	
	/*
	 * Render general settings section info. 
	 * @since 1.0.0
	 */	
	public function cmp_settings_general_info () {
		echo '<p>' . __("General settings for CorvoMap Plugin", GUESTABA_CMP_TEXTDOMAIN) . '</p>';
	}




	/*
     * Render location/address settings section info.
     * @since 1.0.4
     */
	public function cmp_settings_address_section_info () {
		echo '<p>' . __("Address settings for location map.", GUESTABA_CMP_TEXTDOMAIN) . '</p>';
	}



	/*
	 * Places link to settings page under the Plugins->Installed Plugins listing entry.
	 * It is intended to be called via add_filter. 
	 * 
	 * @param array $links an array of existing action links.
	 * 
	 * @return $links with 
	 * @since 1.0.0
	 */
	public function action_links( $links ) {	
	
		array_unshift( $links,'<a href="http://support.guestaba.com/support/home" target="_blank">FAQ</a>' );
		array_unshift($links, '<a href="'. get_admin_url(null, 'options-general.php?page=crowdmap-settings') .'">Settings</a>');
		
    	return $links;
		
		
	}

	
}

?>