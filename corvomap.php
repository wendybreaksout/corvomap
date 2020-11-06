<?php

/**
 * Guestaba CorvoMap Plugin
 *
 * The CrowdMap plugin is a map that enables the display and management of a crowdmap in WordPress.
 *
 * @link              http://guestaba.com
 * @since             1.0.0
 * @package           CorvoMap
 *
 * @wordpress-plugin
 * Plugin Name:       CorvoMap
 * Plugin URI:        http://guestaba.com
 * Description:       The CorvoMap plugin enables the display and management of crowd maps within WordPress.
 * Version:           1.0.4
 * Author:            Guestaba Team
 * Author URI:        http://guestaba.com/team
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       corvomap
 * Domain Path:       /languages
 */

if (!defined('GUESTABA_CMP_PLUGIN_FILE'))
	define('GUESTABA_CMP_PLUGIN_FILE', plugin_basename(__FILE__) );

if (!defined('GUESTABA_CMP_TEXTDOMAIN'))
	define('GUESTABA_CMP_TEXTDOMAIN', 'corvomap');

if (!defined('GUESTABA_CORVOMAP_VERSION_KEY'))
    define('GUESTABA_CORVOMAP_VERSION_KEY', 'guestaba_corvomap_version');

if (!defined('GUESTABA_MAPS_LISTING_PAGE_NAME')) {
	define('GUESTABA_MAPS_LISTING_PAGE_NAME', 'maps-listing');
}

if (!defined('GUESTABA_CMP_OPTIONS_NAME')) {
	define('GUESTABA_CMP_OPTIONS_NAME', 'guestaba_cmp_settings');
}

if (!defined('GUESTABA_CMP_GEO_OPTIONS_NAME')) {
	define('GUESTABA_CMP_GEO_OPTIONS_NAME', 'guestaba_cmp_geo_settings');
}


if (!defined('GUESTABA_CMP_ICON_DIRECTORY_URL')) {
	define('GUESTABA_CMP_ICON_DIRECTORY_URL',  plugin_dir_url( __FILE__ ) . 'public/img/map-icons/');
}


if (!defined('GUESTABA_CMP_ICON_DIRECTORY')) {
	define('GUESTABA_CMP_ICON_DIRECTORY',  plugin_dir_path( __FILE__ ) . 'public/img/map-icons/');
}

if (!defined('GUESTABA_USER_HAS_SUPPORTED')) {
	define('GUESTABA_USER_HAS_SUPPORTED', -1 );
}

if (!defined('GUESTABA_USER_SUPPORT_REGISTERED')) {
	define('GUESTABA_USER_SUPPORT_REGISTERED', true );
}

if (!defined('GUESTABA_POST_SUPPORT_FAILED')) {
	define('GUESTABA_POST_SUPPORT_FAILED', false );
}

if (!defined('GUESTABA_SUPPORT_COMMENT_META_KEY')) {
	define('GUESTABA_SUPPORT_COMMENT_META_KEY', 'support' );
}

if (!defined('GUESTABA_SUPPORT_COMMENT_META_VALUE')) {
	define('GUESTABA_SUPPORT_COMMENT_META_VALUE', '1' );
}

if (!defined('GUESTABA_CORVOMAP_VERSION_NUM'))
	define('GUESTABA_CORVOMAP_VERSION_NUM', '1.0.0');

update_option(GUESTABA_CORVOMAP_VERSION_KEY, GUESTABA_CORVOMAP_VERSION_NUM);

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-crowdmap-activator.php
 */
function activate_crowdmap() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-crowdmap-activator.php';
	CrowdMap_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-crowdmap-deactivator.php
 */
function deactivate_crowdmap() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-crowdmap-deactivator.php';
	CrowdMap_Deactivator::deactivate();
}



register_activation_hook( __FILE__, 'activate_crowdmap' );
register_deactivation_hook( __FILE__, 'deactivate_crowdmap' );


/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-crowdmap.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_crowdmap() {

	$plugin = new CrowdMap();
	$plugin->run();

}
run_crowdmap();
