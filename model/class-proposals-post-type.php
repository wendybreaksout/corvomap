<?php

/**
 * Encapsulates attributes and behavior of the proposals post type.
 *
 * @link       http://guestaba.com
 * @since      1.0.0
 *
 * @package    CrowdMap
 * @subpackage CrowdMap/model
 */

/**
 * Proposals Post Type class
 *
 * Defines attribues and behavior of the Proposals post type
 *
 *
 * @since      1.0.0
 * @package    CrowdMap
 * @subpackage CrowdMap/model
 * @author     Wendy Emerson <wendybreaksout@gmail.com>
 */
class Proposals_Post_Type {
	
	/**
	 *  String to define post type name.
	 *  @since	1.0.0
	 *  @access	protected
	 *  @var String  $post_type  Stores post_type name
	 */
	protected $post_type ;
	
	/**
	 * Array for storing UI labels for Proposals custom post type
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var     array    $labels   Stores UI labels for Proposals CPT
	 */
	protected $labels;
	
	/**
	 * Array for storing argument passed to register_post_type
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var     array    $args   Stores UI labels for Proposals CPT
	 */
	protected $args;
	
	/**
	 * Constructor for Proposals Post Type
	 * Initializes labels and args for registration.
	 * @since    1.0.0
	 */
	
	public function __construct() {
		
		$this->post_type = 'proposals';
		
		$theme = wp_get_theme();
		$text_domain = GUESTABA_CMP_TEXTDOMAIN;

		$this->labels = array(
		    'name'                => __( 'Proposals Listings', $text_domain ),
            'singular_name'       => __( 'Proposal', $text_domain ),
            'menu_name'           => __( 'Proposals', $text_domain ),
            'parent_item_colon'   => __( 'Parent Proposal:', $text_domain ),
            'all_items'           => __( 'All Proposals', $text_domain ),
            'view_item'           => __( 'View Proposal', $text_domain ),
            'add_new_item'        => __( 'Add New Proposal', $text_domain ),
            'add_new'             => __( 'Add New', $text_domain ),
            'edit_item'           => __( 'Edit Proposal', $text_domain ),
            'update_item'         => __( 'Update Proposal', $text_domain ),
            'search_items'        => __( 'Search Proposals', $text_domain ),
            'not_found'           => __( 'No Proposals found', $text_domain ),
            'not_found_in_trash'  => __( 'No Proposals found in Trash', $text_domain )
		);
		
		$this->args = array(
		     'label'               => __( 'Proposals', $text_domain ),
            'labels'              => $this->labels,
            'description'         => __('Display your works by filters','purepress'),
            'supports'            => array( 'title', 'editor','excerpt', 'author', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes', 'thumbnail' ,'comments' ),
            'hierarchical'        => true,
            'public'              => true,

            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,

            'query_var'           => true,
            'publicly_queryable'  => true,

            'exclude_from_search' => false,
            'has_archive'         => false,

            'can_export'          => true,
            'menu_position'       => 5,
            'rewrite'             => array(
                'slug'            => 'proposals',
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
		if ( 'proposals' === get_post_type() ) {
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
			$query->set( 'post_type', array( 'post', 'page', 'proposals' ) );
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
	                    'proposals'
	                ) );
	        }
	    }
    }

    
    /*
	 * Query post type templates. Post support function.
	 * 
	 * Probably never used for this post type. 
	 * 
	 * @since 1.0.0
	 */    
    public function get_custom_post_type_template($template_path) {
  
	    if ( get_post_type() == 'proposals' ) {
	        if ( is_single() ) {
	            // checks if the file exists in the theme first,
	            // otherwise serve the file from the plugin
	            if ( $theme_file = locate_template( array ( 'single-proposals.php' ) ) ) {
	                $template_path = $theme_file;
	            } else {
	                $template_path = trailingslashit( dirname( plugin_dir_path( __FILE__ ) ) ). 'single-proposals.php';
	            }	         	
	        }
	    	elseif ( is_archive() ) {
	            if ( $theme_file = locate_template( array ( 'archive-proposals.php' ) ) ) {
	                $template_path = $theme_file;
	            } else { 
	            	$template_path = trailingslashit( dirname( plugin_dir_path( __FILE__ ) ) ) . 'archive-proposals.php';
	        	}
	    	}    
		}
		return $template_path;
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
	
	        if ( 'proposals' == get_post_type() ) {
	            $truncate = get_post_meta($post->ID, 'meta_proposal_desc', true);
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
	
	/**
	 * 
	 * Get proposal list by post ID . 
	 * 
	 * @since 1.0.0
	 * @param string $postID
	 * @throws Exception if get_post_meta cannot retrieve list.
	 *
	 */
	public static function get_proposal( $post_id ) {

		$post = get_post( $post_id );

		$proposal = self::get_array( $post );

		return $proposal;
	}

	public static function publish_proposal( $proposal ) {

		// Proposals inherit the survey area taxonomy terms assigned to the map.
		$terms = get_the_terms( $proposal['map_id'], 'survey_area');
		$survey_area_ids = array();
		foreach ( $terms as $term ) {
			$survey_area_ids[] = $term->term_id ;
		}

		$survey_area_ids = array_map( 'intval', $survey_area_ids );
		$survey_area_ids = array_unique( $survey_area_ids );

		$pt_term = get_term_by('name', $proposal['proposal_type'], 'proposal_type');
		$pt_term_id = intval( $pt_term->term_id );

		$post_id = wp_insert_post(
			array(
				'post_title' => $proposal['title'],
				'post_content' => $proposal['description'],
				'post_type' => 'proposals',
				'post_status' => 'publish'
			));

		if ( $post_id !== false ) {

			$res = wp_set_object_terms( $post_id, $survey_area_ids, 'survey_area');
			if( is_wp_error( $res )) {
				error_log('Error:' . __FILE__ . ',' . __LINE__  . ',' . __('Could not set survey_areas taxonomy', GUESTABA_CMP_TEXTDOMAIN));
			}

			$res = wp_set_object_terms( $post_id, $pt_term_id, 'proposal_type');
			if( is_wp_error( $res )) {
				error_log('Error:' . __FILE__ . ',' . __LINE__  . ',' . __('Could not set post_type taxonomy', GUESTABA_CMP_TEXTDOMAIN));
			}

			if ( update_post_meta( $post_id, 'lat', $proposal['lat'] ) &&
			     update_post_meta( $post_id, 'lng', $proposal['lng'] ) &&
			     update_post_meta( $post_id, 'map_id', $proposal['map_id'] ) &&
			     update_post_meta( $post_id, 'proposal_type', $proposal['proposal_type'])
			) {
				return self::get_proposal( $post_id );

			}
		}

		return false;
	}

	public static function post_comment( $comment ) {

		global $current_user;

		$commentdata = array(
			'comment_post_ID' => $comment['proposal_id'],
			'comment_author' => $current_user->display_name,
			'comment_author_email' => $current_user->user_email,
			'comment_author_url' => $current_user->user_url,
			'comment_content' => $comment['content'],
			'comment_type' => '',
			'user_id' => $current_user->ID
		);

	//Insert new comment and get the comment ID
		$comment_id = wp_new_comment( $commentdata );
		return $comment_id;
	}


	public static function post_support( $comment ) {

		global $current_user;

		if ( self::user_has_supported( $comment['proposal_id'], $current_user->ID )) {
			return -1;
		}

		$commentdata = array(
			'comment_post_ID' => $comment['proposal_id'],
			'comment_author' => $current_user->display_name,
			'comment_author_email' => $current_user->user_email,
			'comment_author_url' => $current_user->user_url,
			'comment_content' => GUESTABA_SUPPORT_COMMENT_META_VALUE,
			'comment_type' => '',
			'user_id' => $current_user->ID
		);

		//Insert new comment and get the comment ID
		$comment_id = wp_new_comment( $commentdata );



		if ( ! add_comment_meta( $comment_id, GUESTABA_SUPPORT_COMMENT_META_KEY, GUESTABA_SUPPORT_COMMENT_META_VALUE ) ) {
			return false; //failure according to WP codex
		}

		return true;
	}

	public static function get_comments( $proposal_id ) {

		$args = array(
			'post_id' => $proposal_id,
		);
		$comments = get_comments( $args );
		return  $comments;

	}




	public static function get_array( $post ) {

		global $current_user;

		$user = get_user_by( 'id', $post->post_author );
		if ( $user !== false ) {
			$author_name = $user->user_login;
			$user_id = $user->ID;
		} else {
			$author_name = __( 'Anonymous', GUESTABA_CMP_TEXTDOMAIN );
			$user_id = -1;
		}


		$proposal = array(
			'id' => $post->ID,
			'title' => $post->post_title,
			'description' => $post->post_content,
			'author' => $author_name,
			'date' => $post->post_date,
			'timestamp' => get_post_time('U', true, $post),
			'lat' => get_post_meta( $post->ID, 'lat', true),
			'lng' => get_post_meta( $post->ID, 'lng', true),
			'map_id' => get_post_meta( $post->ID, 'map_id', true),
			'proposal_type' => get_post_meta( $post->ID, 'proposal_type', true),
			'comments' => self::get_comments( $post->ID ),
			'support_count' => self::get_support_count( $post->ID ),
			'user_has_supported' => self::user_has_supported( $post->ID, $current_user->ID ),
			'user_id' => $user_id,
			'permalink' => get_post_permalink( $post->ID )
		);

		return $proposal;

	}

	private static function get_support_count( $proposal_id ) {
		$args = array(
			'post_id' => $proposal_id,
		);


		$support_count = 0;
		$comments = get_comments( $args ) ;

		foreach ( $comments as $comment ) {
			$val = get_comment_meta( $comment->comment_ID, 'support', true );
			if ( $val === '1') {
				$support_count++;
			}
		}

		return $support_count;

	}



	private static function user_has_supported ( $proposal_id, $user_id ) {

		// get comments for user for this proposal.
		$args = array(
			'post_id' => $proposal_id,
			'user_id' => $user_id
		);

	$comments = get_comments( $args ) ;

		// If there are no comments, return false.
		if ( count($comments) == 0 ) {
			return false;
		}

		// If there are comments and none of them are 'supports', return false, else return true.
		foreach ( $comments as $comment ) {
			$val = get_comment_meta( $comment->comment_ID, 'support', true );
			if ( $val === '1') {
				return true;
			}
		}

		return false;

	}


}
?>
