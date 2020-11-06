<?php

/**
 *
 * Date: 11/12/15
 * Time: 8:33 PM
 */
class Proposal_Type_Taxonomy {

	protected $taxonomy_key = 'proposal_type';
	protected $labels;
	protected $args;


	/**
	 * Study_Area_Taxonomy constructor.
	 */

	public function __construct() {

		$this->labels = array(
			'name'                       => _x( 'Proposal Types', 'Taxonomy General Name', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'singular_name'              => _x( 'Proposal Type', 'Taxonomy Singular Name', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'menu_name'                  => __( 'Proposal Type', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'all_items'                  => __( 'All Proposal Types', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'parent_item'                => __( 'Parent Proposal Type', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'parent_item_colon'          => __( 'Parent Proposal Type:', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'new_item_name'              => __( 'New Proposal Type', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'add_new_item'               => __( 'Add Proposal Type', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'edit_item'                  => __( 'Edit Proposal Type', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'update_item'                => __( 'Update Proposal Type', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'view_item'                  => __( 'View Proposal Type', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'separate_items_with_commas' => __( 'Separate Proposal Types  with commas', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'add_or_remove_items'        => __( 'Add or remove Proposal Types', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'popular_items'              => __( 'Popular Proposal Types', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'search_items'               => __( 'Search Proposal Types', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'not_found'                  => __( 'Not Found', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'items_list'                 => __( 'Proposal Type list', 'GUESTABA_CMP_TEXTDOMAIN' ),
			'items_list_navigation'      => __( 'Proposal Type list navigation', 'GUESTABA_CMP_TEXTDOMAIN' ),
		);

		$this->args = array(
			'labels'                     => $this->labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'update_count_callback'      => array( $this, 'proposal_type_count_change' ),
		);

	}

	public function register() {
		register_taxonomy( $this->taxonomy_key, array( 'proposals' ), $this->args );
	}

	public function proposal_type_count_change() {

	}


}