<?php

/**
 * Encapsulates attributes and behavior of the map post type.
 *
 * @link       http://guestaba.com
 * @since      1.0.0
 *
 * @package    CrowdMap
 * @subpackage CrowdMap/model
 */

/**
 * Map Post Type class
 *
 * Defines attribues and behavior of the Map post type
 *
 *
 * @since      1.0.0
 * @package    CrowdMap
 * @subpackage CrowdMap/model
 * @author     Wendy Emerson <wendybreaksout@gmail.com>
 */
class Maps_Post_Type {
	
	/**
	 *  String to define post type name.
	 *  @since	1.0.0
	 *  @access	protected
	 *  @var String  $post_type  Stores post_type name
	 */
	protected $post_type ;
	
	/**
	 * Array for storing UI labels for Maps custom post type
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var     array    $labels   Stores UI labels for Map CPT
	 */
	protected $labels;
	
	/**
	 * Array for storing argument passed to register_post_type
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var     array    $args   Stores UI labels for Map CPT
	 */
	protected $args;
	
	/**
	 * Constructor for Map Post Type
	 * Initializes labels and args for registration.
	 * @since    1.0.0
	 */
	
	public function __construct() {
		
		$this->post_type = 'maps';
		
		$theme = wp_get_theme();
		$text_domain = $theme->get('TextDomain');

		$this->labels = array(
		    'name'                => __( 'Map Listings', $text_domain ),
            'singular_name'       => __( 'Map', $text_domain ),
            'menu_name'           => __( 'Maps', $text_domain ),
            'parent_item_colon'   => __( 'Parent Map:', $text_domain ),
            'all_items'           => __( 'All Maps', $text_domain ),
            'view_item'           => __( 'View Map', $text_domain ),
            'add_new_item'        => __( 'Add New Map', $text_domain ),
            'add_new'             => __( 'Add New', $text_domain ),
            'edit_item'           => __( 'Edit Map', $text_domain ),
            'update_item'         => __( 'Update Map', $text_domain ),
            'search_items'        => __( 'Search Maps', $text_domain ),
            'not_found'           => __( 'No maps found', $text_domain ),
            'not_found_in_trash'  => __( 'No maps found in Trash', $text_domain )
		);
		
		$this->args = array(
		     'label'               => __( 'Maps', $text_domain ),
            'labels'              => $this->labels,
            'description'         => __('Map description goes here.', GUESTABA_CMP_TEXTDOMAIN ),
            'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes', 'thumbnail' ),
            'hierarchical'        => true,
            'public'              => true,

            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,

            'query_var'           => true,
            'publicly_queryable'  => true,

            'exclude_from_search' => false,
            'has_archive'         => true,

            'can_export'          => true,
            'menu_position'       => 5,
            'rewrite'             => array(
                'slug'            => 'maps',
                'with_front'      => true,
                'pages'           => true,
                'feeds'           => true,
            ),
            'capability_type'     => 'post',
            'taxonomies'          => array( 'category', 'post_tag' )
		);

	}

	/*
	 * Register post type
	 * 
	 * @since 1.0.0
	 * 
	 * @param none
	 * @return void
	 */
	public function register() {
		register_post_type( $this->post_type, $this->args);
	}
	
	
	/*
	 * Remove post actions
	 * 
	 * @since 1.0.0
	 * 
	 * @param none
	 * @return void
	 */	
	public function remove_post_actions($actions) {
		if ( 'maps' === get_post_type() ) {
            unset( $actions['trash'] );
        }
        return $actions;;
	}
	
	
	/*
	 * Get page by slug. Post support function.
	 * 
	 * @since 1.0.0
	 */	
	public function get_page_by_slug($page_slug, $output = OBJECT, $post_type = 'page' ) {
    	global $wpdb;
    	$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s", $page_slug, $post_type ) );
    	if ( $page )
            return get_page($page, $output);
    	return null;
	}

	
	/*
	 * Add to query. Post support function.
	 * 
	 * @since 1.0.0
	 */
	public function add_to_query( $query ) {
		if ( is_home() && $query->is_main_query()  && $query->is_search  && $query->is_category )
			$query->set( 'post_type', array( 'post', 'page', 'maps' ) );
		return $query;
	}

	/*
	 * Query post type. Post support function.
	 * 
	 * @since 1.0.0
	 */
    public function query_my_post_types( &$query ) {
	    // Do this for all category and tag pages, can be extended to is_search() or is_home() ...
	    if ( is_category() || is_tag() ) {
	        $post_type = $query->get( 'post_type' );
	        // ... if no post_type was defined in query then set the defaults ...
	        if ( empty( $post_type ) ) {
	            $query->set( 'post_type', array(
	                    'post',
	                    'maps'
	                ) );
	        }
	    }
    }
    
    

  


	
    /*
	 * Truncate post. Post support function.
	 * 
	 * Probably never used for this post type. 
	 * 
	 * @since 1.0.0
	 */	
	public function truncate_post( $amount, $echo = true, $post = '' ) {
	    global $shortname;
	    if ( '' == $post ) global $post;
	    $post_excerpt = '';
	    $post_excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );
	    if ( 'on' == et_get_option( $shortname . '_use_excerpt' ) && '' != $post_excerpt ) {
	        if ( $echo ) echo $post_excerpt;
	        else return $post_excerpt;
	    } else {
	
	        if ( 'maps' == get_post_type() ) {
	            $truncate = get_post_meta($post->ID, 'meta_map_desc', true);
	        } else {
	            $truncate = $post->post_content;
	        }
	
	        // remove caption shortcode from the post content
	        $truncate = preg_replace('@\[caption[^\]]*?\].*?\[\/caption]@si', '', $truncate);
	        // apply content filters
	        $truncate = apply_filters( 'the_content', $truncate );
	        // decide if we need to append dots at the end of the string
	        if ( strlen( $truncate ) <= $amount ) {
	            $echo_out = '';
	        } else {
	            $echo_out = '...';
	            // $amount = $amount - 3;
	        }
	        // trim text to a certain number of characters, also remove spaces from the end of a string ( space counts as a character )
	        $truncate = rtrim( wp_trim_words( $truncate, $amount, '' ) );
	        // remove the last word to make sure we display all words correctly
	        if ( '' != $echo_out ) {
	            $new_words_array = (array) explode( ' ', $truncate );
	            array_pop( $new_words_array );
	            $truncate = implode( ' ', $new_words_array );
	            // append dots to the end of the string
	            $truncate .= $echo_out;
	        }
	        if ( $echo ) echo $truncate;
	        else return $truncate;
	    };
	}

	public static function get_crowdmap_proposals( $map_id, $since=0 ) {


		$post_type = 'proposals' ;


		$args = array(
			'post_type'      => isset( $post_type ) ? $post_type : 'post',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		);


		if ( $since != 0 ) {

			// truncate microseconds sent with client timestamp
			$since = round( $since / 1000 );

			$args['date_query'] = array(
				'after' => date('Y-m-d H:i:s', $since)
			);

		}


		$post_query = new WP_Query( $args );

		$proposals = array();

		while ( $post_query->have_posts() ) : $post_query->the_post();
			$proposal_id = get_the_ID();
			$proposal_map_id = get_post_meta( $proposal_id, 'map_id', true );
			if ( isset( $proposal_map_id ) && $proposal_map_id == $map_id ) {
				$proposals[] = Proposals_Post_Type::get_array( get_post( $proposal_id));
			}

		endwhile;

		wp_reset_postdata();

		return $proposals;

	}

	public static function get_crowdmap_proposals_ranked( $map_id ) {

		$proposals = self::get_crowdmap_proposals( $map_id );
		usort( $proposals, function( $a, $b ) {
			return( $b['support_count']  -  $a['support_count'] );
		} );

		return $proposals;

	}

	public static function get_map_activity( $map_id, $since=0) {

		$proposals = self::get_crowdmap_proposals( $map_id );

		$activity_list = array();

		foreach ( $proposals as $proposal ) {


			// TODO: avatar size should be configurable
			if ( $proposal['user_id'] < 0 ) {
				$avatar = get_avatar( 'nobody', 24);
			}
			else {
				$avatar = get_avatar( $proposal['user_id'], 24);
			}


			$activity = array(
				'id' => 'id',
				'activity_type' => __('Proposal', GUESTABA_CMP_TEXTDOMAIN),
				'description' => $proposal['title'],
				'lat' => $proposal['lat'],
				'lng' => $proposal['lng'],
				'date' => $proposal['timestamp'],
				'author' => $proposal['author'],
				'avatar' => $avatar
			);

			$activity_list[] = $activity;

			foreach ( $proposal['comments'] as $comment ) {

				$avatar = get_avatar( $comment->user_id, 24);

				if ( get_comment_meta( $comment->comment_ID, 'support', true ) === '1') {
					$activity_type = __('Support', GUESTABA_CMP_TEXTDOMAIN ) . ' ' . __('for', GUESTABA_CMP_TEXTDOMAIN) . ' &quot;' . $proposal['title'] . '&quot' ;
					$description = '';
				}
				else {
					$activity_type = __('Comment', GUESTABA_CMP_TEXTDOMAIN ) . ' ' . __('for', GUESTABA_CMP_TEXTDOMAIN) . ' &quot;' . $proposal['title'] . '&quot' ;
					$description = $comment->comment_content ;
				}

				$activity = array(
					'id' => $comment->comment_ID,
					'activity_type' => $activity_type,
					'description' => $description,
					'lat' => $proposal['lat'],
					'lng' => $proposal['lng'],
					'date' => strtotime("{$comment->comment_date_gmt} GMT"),
					'author' => $comment->comment_author,
					'avatar' => $avatar
				);
				$activity_list[] = $activity;
			}

		}

		usort( $activity_list, function( $a, $b ) {
			// return( strtotime( $b['date'] ) - strtotime( $a['date'] ));
			return( $b['date']  -  $a['date'] );
		} );



		$activity_list_filtered = array_filter( $activity_list, function( $a ) use ($since) {
			// return ( strtotime( $a['date'] ) * 1000 )  > $since  ;
			return (  $a['date']  * 1000 )  > $since  ;
		} );

		return $activity_list_filtered;

	}

	public static function get_all_maps() {

		$post_type = 'maps' ;

		$args = array(
			'post_type'      => isset( $post_type ) ? $post_type : 'post',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		);


		$post_query = new WP_Query( $args );

		$maps = array();

		while ( $post_query->have_posts() ) : $post_query->the_post();
			$map_id = get_the_ID();
			if ( isset( $map_id ) ) {
				$maps[] = Maps_Post_Type::get_array( get_post( $map_id ));
			}

		endwhile;

		wp_reset_postdata();

		return $maps;
	}

	public static function get_array( $post ) {

		$map = array(
			'id' => $post->ID,
			'title' => $post->post_title,
		);

		return $map;
	}
}
?>