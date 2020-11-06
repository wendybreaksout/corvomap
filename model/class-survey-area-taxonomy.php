<?php

/**
 * Created by PhpStorm.
 * User: weskempferjr
 * Date: 11/12/15
 * Time: 8:11 PM
 */
class Survey_Area_Taxonomy {

	protected $taxonomy_key = 'survey_area';
	protected $labels;
	protected $args;


	/**
	 * Survey_Area_Taxonomy constructor.
	 */

	public function __construct() {

		$this->labels = array(
			'name'                       => _x( 'Survey Areas', 'Taxonomy General Name', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'singular_name'              => _x( 'Survey Area', 'Taxonomy Singular Name', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'menu_name'                  => __( 'Survey Area', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'all_items'                  => __( 'All Survey Areas', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'parent_item'                => __( 'Parent Survey Area', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'parent_item_colon'          => __( 'Parent Survey Area:', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'new_item_name'              => __( 'New Survey Area', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'add_new_item'               => __( 'Add Survey Area', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'edit_item'                  => __( 'Edit Survey Area', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'update_item'                => __( 'Update Survey Area', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'view_item'                  => __( 'View Survey Area', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'separate_items_with_commas' => __( 'Separate survey areas  with commas', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'add_or_remove_items'        => __( 'Add or remove survey areas', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'popular_items'              => __( 'Popular Survey Areas', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'search_items'               => __( 'Search Survey Areas', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'not_found'                  => __( 'Not Found', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'items_list'                 => __( 'Survey Area list', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'items_list_navigation'      => __( 'Survey Area list navigation', 'GUESTABA_CMP_TEXTDOMAIN' ),
		);

		$this->args = array(
			'labels'                     => $this->labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'update_count_callback'      => array( $this, 'survey_area_count_change' ),
		);

	}

	public function register() {
		register_taxonomy( $this->taxonomy_key, array('maps', 'proposals' ), $this->args );
	}

	public function survey_area_count_change() {

	}


}