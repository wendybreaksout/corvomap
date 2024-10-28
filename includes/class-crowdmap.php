<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    CrowdMap
 * @subpackage CrowdMap/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    CrowdMap
 * @subpackage CrowdMap/includes
 * @author     Wendy Emerson <wendybreaksout@gmail.com>
 */
class CrowdMap {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      CrowdMap_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $crowdmap    The string used to uniquely identify this plugin.
	 */
	protected $crowdmap;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	
	
	/**
	 * Stores the name of the maps post type.
	 * @since   1.0.0
	 * @access 	private
	 * @var		Maps_Post_Type $maps_post_type  The maps post type instance.
	 */
	protected $maps_post_type ;

	
	/**
	 * Stores name of proposals post type.
	 * @since   1.0.0
	 * @access 	private
	 * @var		Proposals_Post_Type $maps_post_type  The maps post type instance.
	 */
	protected $proposals_post_type ;

	/*
	 * Store instance of survey area taxonomy classes
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Survey_Area_Taxonomy $survey_area_taxonomy
	 */
	protected $survey_area_taxonomy ;

	/*
	 * Store instance of proposal type taxonomy classes
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Propoal_Type_Taxonomy $proposal_type_taxonomy
	 */
	protected $proposal_type_taxonomy ;


	/*
	 * Store instance of Maps_View_Manager
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var CrowdMap_View_Manager $crowdmap_view_manager
	 */
	protected $crowdmap_view_manager ;

	/*
	 *
	 */

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	
	public function __construct() {

		$this->crowdmap = GUESTABA_CMP_TEXTDOMAIN;
		$this->version = GUESTABA_CORVOMAP_VERSION_NUM;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->register_post_types();
		$this->register_taxonomies();
		$this->register_shortcodes();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - CrowdMap_Loader. Orchestrates the hooks of the plugin.
	 * - CrowdMap_i18n. Defines internationalization functionality.
	 * - CrowdMap_Admin. Defines all hooks for the dashboard.
	 * - CrowdMap_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {


		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-crowdmap-logger.php';
		
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-crowdmap-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-crowdmap-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-crowdmap-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-crowdmap-public.php';
		
		/**
		 * The class responsible for defining maps custom post type
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'model/class-maps-post-type.php';
		

		/**
		 * The class responsible for defining proposals custom post type
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'model/class-proposals-post-type.php';

		/**
		 * The class responsible for defining survey area custom taxonomy
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'model/class-survey-area-taxonomy.php';

		/**
		 * The class responsible for defining maps custom post type
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'model/class-proposal-type-taxonomy.php';


		/**
		 * Class file for admin/settings page
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-crowdmap-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-crowdmap-menu-pages.php';

		/**
		 * Class files for post type meta boxes
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-crowdmap-meta-box.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-crowdmap-maps-meta-box.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-crowdmap-proposals-meta-box.php';

		/**
		 * The class responsible defining and adding shortcodes. 
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-crowdmap-shortcodes.php';


		/**
		 * CrowdMap_View_Manager class is responsible for displaying maps.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-crowdmap-view-manager.php';
			
		$this->loader = new CrowdMap_Loader();
		$this->maps_post_type = new Maps_Post_Type();
		$this->proposals_post_type = new Proposals_Post_Type();
		$this->survey_area_taxonomy = new Survey_Area_Taxonomy();
		$this->proposal_type_taxonomy = new Proposal_Type_Taxonomy();
		$this->crowdmap_view_manager = new CrowdMap_View_Manager();

		/**
		 * The classes responsible for handling ajax requests
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-crowdmap-ajax.php';





		/*
		 * The client template manager returns templates to be used on the client side.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-crowdmap-client-template-manager.php';
		
	}

	
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the CrowdMap_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new CrowdMap_i18n();
		$plugin_i18n->set_domain( $this->get_crowdmap() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		global $post ;

		$this->loader->add_action('plugins_loaded', $this, 'upgrade_data');

		$plugin_admin = new CrowdMap_Admin( $this->get_crowdmap(), $this->get_version() );
		

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'localize_scripts');		
		$this->loader->add_action( 'views_edit-maps', $this->maps_post_type, 'remove_post_actions' );
		$this->loader->add_filter( 'page_row_actions', $this->maps_post_type, 'remove_post_actions' );
		$this->loader->add_action('parse_query',$this->maps_post_type,'query_my_post_types' );
		$this->loader->add_action('pre_get_posts',$this->maps_post_type,'add_to_query' );
		
		


		$cmp_settings = new CrowdMap_Settings();
		$cmp_menu_pages = new CrowdMap_Menu_Pages();

		if ( is_admin() ) {
			$this->loader->add_action( 'admin_menu', $cmp_settings, 'add_cmp_options_page' );
			$this->loader->add_action( 'admin_init', $cmp_settings, 'settings_init' );
			$this->loader->add_action( 'admin_menu', $cmp_menu_pages, 'admin_menu_pages' );

		}

		$this->loader->add_action('plugin_action_links_' . GUESTABA_CMP_PLUGIN_FILE, $cmp_settings, 'action_links');






		switch ( $this->get_current_post_type() ) {
			case 'maps':
			case 'edit-maps':
				$maps_meta_box = new CrowdMap_Maps_Meta_Box();
				$this->loader->add_action( 'add_meta_boxes', $maps_meta_box, 'meta_box_init' );
				$this->loader->add_action( 'admin_menu', $maps_meta_box, 'remove_meta_boxes' );
				$this->loader->add_action( 'save_post', $maps_meta_box, 'post_meta_save' );
				break;

			case 'proposals':
			case 'edit-proposals':
				$proposals_meta_box = new CrowdMap_Proposals_Meta_Box();
				$this->loader->add_action( 'add_meta_boxes', $proposals_meta_box, 'meta_box_init' );
				$this->loader->add_action( 'admin_menu', $proposals_meta_box, 'remove_meta_boxes' );
				$this->loader->add_action( 'save_post', $proposals_meta_box, 'post_meta_save' );
				break;


			default:
				break;
		}

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new CrowdMap_Public( $this->get_crowdmap(), $this->get_version() );
		
		$ajax_controller = new CrowdMap_Public_Ajax_Controller();
		
		

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'localize_scripts');
		$this->loader->add_action( 'widgets_init', $plugin_public, 'register_widget_areas' ); 
	
	
		$this->loader->add_action('wp_ajax_nopriv_crowdmap_ajax' , $ajax_controller, 'crowdmap_ajax');
		$this->loader->add_action('wp_ajax_crowdmap_ajax' , $ajax_controller, 'crowdmap_ajax');
		
		$this->loader->add_filter( 'the_content', $this->crowdmap_view_manager, 'display_maps_post' );

		// TODO: How to handle archive listing for this post type is to-be-determined. This is one approach:
		// $this->loader->add_filter( 'archive_template', $this->maps_post_type, 'get_custom_post_type_template' );






	}
	/**
	 * Register custom post types
	 * @since 	1.0.0
	 * @access 	private
	 */
	private function register_post_types() {
		$this->loader->add_action('init', $this->maps_post_type, 'register');
		$this->loader->add_action('init', $this->proposals_post_type, 'register');
	}

	/**
	 * Register custom taxonomies
	 * @since 	1.0.0
	 * @access 	private
	 */
	private function register_taxonomies() {
		$this->loader->add_action('init', $this->survey_area_taxonomy, 'register');
		$this->loader->add_action('init', $this->proposal_type_taxonomy, 'register');
	}
	
	/**
	 * Register shortcodes
	 */
	private function register_shortcodes() {
		$shortcodes = new CrowdMap_Shortcodes();
		$shortcodes->register_shortcodes();
	}

	
	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_crowdmap() {
		return $this->crowdmap;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    CrowdMap_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}


	private function get_current_post_type() {
		if ( isset( $_REQUEST['post_type'] )  ) {
			return $_REQUEST['post_type'];
		}
		elseif (isset( $_REQUEST['screen_id'] ) ) {
			return $_REQUEST['screen_id'];
		}
		elseif (isset( $_POST['screen_id'] ) ) {
			return $_POST['screen_id'];
		}
		else {
			if ( isset( $_REQUEST['post'])) {
				$post_type = get_post_type( $_REQUEST['post'] );
				return $post_type;
			}
		}
	}

	/**
	 * Function: update_meta_data
	 *
	 * This function, if necessary, updates the meta data structure if it detects that the plugin
	 * has been updated by comparing the plugin's current version number with that saved in options.
	 *
	 *
	 * @param none
	 * @return void
	 *
	 * @since 1.0.3
	 *
	 */
	public function upgrade_data() {

		if ( current_user_can( 'activate_plugins' ) ) {

			$option = get_option( GUESTABA_CMP_OPTIONS_NAME );
			if ( !isset( $option['version']) || version_compare( $option['version'], $this->version)  < 0 ) {

				// Add new options here following the pattern exemplified by this first if statement.
				// To avoid loss of user settings, be sure to check if option is already set.

				// added 1.0.0
				if (!isset( $option['version']) || version_compare( $option['version'], $this->version)  < 0 ) {
					$option['version'] = $this->version;
				}

				if (!isset( $option['cmp_google_maps_api_key'])) {
					$option['cmp_google_maps_api_key'] = '';
				}

				if (!isset( $option['cmp_google_geocode_api_key'])) {
					$option['cmp_google_geocode_api_key'] = '';
				}


				// added 1.0.5...n would go here.

				// update option table
				update_option( GUESTABA_CMP_OPTIONS_NAME, $option );
				// $this->loader->add_action('init', $this, 'upgrade_meta_data');
				$cmp = new CrowdMap();
				add_action('init', array( $cmp, 'upgrade_meta_data')) ;



			}
		}
	}

	/**
	 * Function: upgrade_meta_data()
	 *
	 * Upgrades meta data structure as required by new features and other code modifications.
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return void
	 *
	 *
	 */
	public function upgrade_meta_data() {
		

	}




}
