<?php
/**
 * Class CrowdMap_Maps_Meta_Box
 *
 * This class defines the appearance and behavior of the metbox associated with
 * the proposal custom post type.
 */

class CrowdMap_Proposals_Meta_Box extends CrowdMap_Meta_Box {


	public function __construct() {
		$this->setPostType( 'proposals' );
		$this->setMetaBoxID(  'proposals_cpt_meta_box' );
		$this->setMetaBoxTitle(  __( 'Proposals Options', GUESTABA_CMP_TEXTDOMAIN ) );
		$this->setNonceId( 'proposals_mb_nonce');
		$this->init_tooltips();
	}

	/**
	 * Function meta_box_render
	 *
	 * This is the render callback function for the proposals CPT metabox.
	 *
	 * @param none
	 * @return void
	 */

	public function meta_box_render() {
		global $post ;

		wp_nonce_field( basename( __FILE__ ), $this->getNonceId() );
		$post_ID = $post->ID;

		$enq_media_args = array( 'post' => $post_ID );
		wp_enqueue_media( $enq_media_args );


		echo '<div class="gst_settings_container">';

		$this->section_heading(__('Proposal Settings', GUESTABA_CMP_TEXTDOMAIN), 'gst-mb-content-settings');


		$this->number_input(  __('Latitude', GUESTABA_CMP_TEXTDOMAIN),
			get_post_meta( $post_ID, 'meta_proposal_lat', true),
			'meta_proposal_lat',
			45.5188697
		);

		$this->number_input(  __('Longitude', GUESTABA_CMP_TEXTDOMAIN),
			get_post_meta( $post_ID, 'meta_proposal_lng', true),
			'meta_proposal_lng',
			-122.6814701
		);

		echo '</div>';
	}

	/**
	 * Function post_meta_save
	 *
	 * This is  post meta data save callback function.
	 *
	 * @param integer $post_id the post ID for the submitted meta data.
	 */

	public function post_meta_save( $post_id ) {

		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ $this->getNonceId()] ) && wp_verify_nonce( $_POST[ $this->getNonceId() ], basename( __FILE__ ) ) ) ? 'true' : 'false';

		// Exits script depending on save status
		if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
			return;
		}


		$this->update_meta_float( $post_id, 'meta_proposal_lat');
		$this->update_meta_float( $post_id, 'meta_proposal_lng');


	}

	/*
	 * Function init_tooltips
	 *
	 * This function initializes the tooltips for the UI elements of this metabox.
	 *
	 * @param none
	 *
	 * @return void
	 */
	protected function init_tooltips() {
		$tooltips = array(
			'add_button' => __( 'Click this button to add a new item to this list.', GUESTABA_CMP_TEXTDOMAIN ),
			'meta_proposal_list' => __('For each price panel, enter date ranges during which a price will be in effect. Enter a price only for Sunday to set the price the same for all days of the week. Click the + button below to add a new price panel.', GUESTABA_CMP_TEXTDOMAIN),
			'delete_proposal_button' => __('Click here to delete this proposal panel.', GUESTABA_CMP_TEXTDOMAIN)
		);
		$this->set_tooltips( $tooltips );
	}


	/**
	 * Function remove_meta_boxes
	 *
	 * Removes other metaboxes on the dashboard that are not pertinent to the proposals custom post type.
	 *
	 * @param none
	 * @return void
	 */
	public function remove_meta_boxes () {
		remove_meta_box('revisionsdiv', 'proposals', 'norm');
		remove_meta_box('slugdiv', 'proposals', 'norm');
		remove_meta_box('authordiv', 'proposals', 'norm');
		remove_meta_box('postcustom', 'proposals', 'norm');
		remove_meta_box('postexcerpt', 'proposals', 'norm');
		remove_meta_box('trackbacksdiv', 'proposals', 'norm');
		remove_meta_box('commentsdiv', 'proposals', 'norm');
		remove_meta_box('pageparentdiv', 'proposals', 'norm');
	}





	/**
	 * Function get_proposal_template
	 *
	 * This function is called by get_proposal_list when there are no proposals for
	 * a maps post. It returns the empty structure of a proposal_list.
	 *
	 * @access private
	 *
	 * @param none
	 *
	 * @return array|mixed
	 */

	private function get_proposal_template () {

		$template = array();
		$template[] = array(
			'title'                    => '' ,
			'meta_map_proposal_date01' =>
				array(
					'date_start' => '' ,
					'date_end'   => ''
				),
			'meta_map_proposal_date02' =>
				array(
					'date_start' => '',
					'date_end'   => ''
				),
			'meta_map_proposal_date03' =>
				array(
					'date_start' => '',
					'date_end'   => ''
				),
			'meta_map_price'          => '',
			'dow_price' => array(
				'sunday' => '',
				'monday' => '',
				'tuesday' => '',
				'wednesday' => '',
				'thursday' => '',
				'friday' => '',
				'saturday' => ''
			)
		);

		return $template;
	}

}