<?php


/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    CrowdMap
 * @subpackage CrowdMap/includes
 * @author     Wendy Emerson <wendybreaksout@gmail.com>
 */
class CrowdMap_Activator {

	/**
	 * Runs at activation time. It currently adds settings defaults via
	 * and instance of the CrowdMap_Settings class.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {	
		if ( current_user_can( 'activate_plugins') ) {	
			$settings = new CrowdMap_Settings();
			$settings->add_option_defaults();
		}
	}

}
