<?php


/**
 * Class CrowdMap_Maps_Meta_Box
 *
 * This class defines the appearance and behavior of the metbox associated with
 * the maps custom post type.
 */
class CrowdMap_Maps_Meta_Box extends CrowdMap_Meta_Box {

	/**
	 * Class constructor
	 * @since 1.0.0
	 * @param none
	 * @return an instance of CrowdMap_Maps_Metabox
	 *
	 */
	public function __construct() {

		$this->setPostType( 'maps' );
		$this->setMetaBoxID(  'maps_cpt_meta_box' );
		$this->setMetaBoxTitle(  __( 'Maps Display Options', GUESTABA_CMP_TEXTDOMAIN ) );
		$this->setNonceId( 'maps_mb_nonce');
		$this->init_tooltips();
	}


	/**
	 * Function remove_meta_boxes
	 *
	 * Removes other metaboxes on the dashboard that are not pertinent to the maps custom post type.
	 *
	 * @param none
	 * @return void
	 */
	public function remove_meta_boxes () {
		remove_meta_box('revisionsdiv', 'maps', 'norm');
		remove_meta_box('slugdiv', 'maps', 'norm');
		remove_meta_box('authordiv', 'maps', 'norm');
		remove_meta_box('postcustom', 'maps', 'norm');
		remove_meta_box('postexcerpt', 'maps', 'norm');
		remove_meta_box('trackbacksdiv', 'maps', 'norm');
		remove_meta_box('commentsdiv', 'maps', 'norm');
		remove_meta_box('pageparentdiv', 'maps', 'norm');
	}


	/**
	 * Function meta_box_render
	 *
	 * This is the render callback function for the maps CPT metabox.
	 *
	 * @param none
	 * @return void
	 */
	public function meta_box_render( ) {

		global $post ;


		wp_nonce_field( basename( __FILE__ ), $this->getNonceId() );

		$post_ID = $post->ID;

		$enq_media_args = array( 'post' => $post_ID );
		wp_enqueue_media( $enq_media_args );


		/*
		 * Map settings
		 */


		echo '<div class="gst_settings_container">';

		$this->section_heading('Map Settings', 'gst-mb-map-settings');

		$this->number_input(  __('Center Latitude', GUESTABA_CMP_TEXTDOMAIN),
			get_post_meta( $post_ID, 'meta_map_center_lat', true),
			'meta_map_center_lat',
			45.5188697,
			array( 'min' => '0', 'max' => '90', 'step' => '0.0000001')
		);

		$this->number_input(  __('Center Longitude', GUESTABA_CMP_TEXTDOMAIN),
			get_post_meta( $post_ID, 'meta_map_center_lng', true),
			'meta_map_center_lng',
			-122.6814701,
			array( 'min' => '-180', 'max' => '180', 'step' => '0.0000001')
		);

		$this->number_input(  __('Zoom', GUESTABA_CMP_TEXTDOMAIN),
			get_post_meta( $post_ID, 'meta_map_zoom', true),
			'meta_map_zoom',
			12
		);

		$this->number_input(  __('SW Bounds Latitude', GUESTABA_CMP_TEXTDOMAIN),
			get_post_meta( $post_ID, 'meta_map_sw_bnds_lat', true),
			'meta_map_sw_bnds_lat',
			0,
			array( 'min' => '0', 'max' => '90', 'step' => '0.0000001')
		);

		$this->number_input(  __('SW Bounds Longitude', GUESTABA_CMP_TEXTDOMAIN),
			get_post_meta( $post_ID, 'meta_map_sw_bnds_lng', true),
			'meta_map_sw_bnds_lng',
			0,
			array( 'min' => '-180', 'max' => '180', 'step' => '0.0000001')
		);

		$this->number_input(  __('NE Bounds Latitude', GUESTABA_CMP_TEXTDOMAIN),
			get_post_meta( $post_ID, 'meta_map_ne_bnds_lat', true),
			'meta_map_ne_bnds_lat',
			0,
			array( 'min' => '0', 'max' => '90', 'step' => '0.0000001')
		);

		$this->number_input(  __('NE Bounds Longitude', GUESTABA_CMP_TEXTDOMAIN),
			get_post_meta( $post_ID, 'meta_map_ne_bnds_lng', true),
			'meta_map_ne_bnds_lng',
			0,
			array( 'min' => '-180', 'max' => '180', 'step' => '0.0000001')
		);

		echo '</div>';

		echo '<div class="gst_settings_container">';

		$this->section_heading('Proposal Type and Icon Settings', 'gst-mb-map-proposal-type-settings');

		$proposal_type_item_labels = array( 'title' => __('Name', GUESTABA_CMP_TEXTDOMAIN),
		                                    'icon' => __('Icon', GUESTABA_CMP_TEXTDOMAIN),
											'marker_color' => __('Marker Color', GUESTABA_CMP_TEXTDOMAIN),
											'icon_color' => __('Icon Color', GUESTABA_CMP_TEXTDOMAIN)
										);
		echo '<p class="cmp-meta-box-message">' .
		        __('In this section add at least one proposal type. A proposal type represents the type of project or facility to be located as indicated on the map.', GUESTABA_CMP_TEXTDOMAIN ) . '</p>';
		echo '<p class="cmp-meta-box-message">' . sprintf( __( 'Use this %s to find icons for map markers.',GUESTABA_CMP_TEXTDOMAIN ), '<a href="http://map-icons.com/" target="_blank">' . __('link to Map Icons', GUESTABA_CMP_TEXTDOMAIN) . '</a>' ) . '</p>';


		$this->sortable_editable_list( __('Proposal Types', GUESTABA_CMP_TEXTDOMAIN),
			$this->get_proposal_types( $post_ID ),
			'meta_proposal_type_list',
			array( $this, 'item_as_proposal_type'),
			'proposal-type-edit-list',
			$proposal_type_item_labels,
			'gst-sort-edit-proposal-type-add',
			true
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

		// Checks save status
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ $this->getNonceId()] ) && wp_verify_nonce( $_POST[ $this->getNonceId() ], basename( __FILE__ ) ) ) ? 'true' : 'false';

		// Exits script depending on save status
		if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
			return;
		}

		$this->update_meta_float( $post_id, 'meta_map_center_lat');
		$this->update_meta_float( $post_id, 'meta_map_center_lng');

		$this->update_meta_float( $post_id, 'meta_map_sw_bnds_lat');
		$this->update_meta_float( $post_id, 'meta_map_sw_bnds_lng');

		$this->update_meta_float( $post_id, 'meta_map_ne_bnds_lat');
		$this->update_meta_float( $post_id, 'meta_map_ne_bnds_lng');

		$this->update_meta_integer( $post_id, 'meta_map_zoom');

		$this->update_proposal_type_array( $post_id );

		$this->update_survey_area_taxonomy( $post_id );


	}




	/**
	 *
	 * Function items_as_string
	 *
	 * @todo this function (item_as_string) does not seem to be used anywhere. Verify that and delete it if not.
	 *
	 * @param $item
	 *
	 * @return string
	 */
	protected function item_as_string($item ) {
		return strval($item );
	}

	/**
	 *
	 * Function item_as_text_input
	 *
	 * This is an item callback function for sortable_editable_list(). It returns a string containing the
	 * HTML text input elements for each member of a post meta data string array.
	 *
	 * @todo this is a generic function that can be moved into the superclass.
	 *
	 * @param string $name the name of the individual form element for the item.
	 * @param string $class a user defined class.
	 * @param mixed $item the post meta data array element.
	 *
	 * @return string
	 */

	protected function item_as_text_input( $name, $class, $item ) {

		$value = $item['title'];
		$input_tag = '<div class="gst-sort-edit-input gst_input_group">';
		// $input_tag .= '<label for="' . $name . '">' . $labels['title'] . '</label>';
		$input_tag .= '<input title="Drag this item to change it position in the order." type="text"  name="'. $name . '-title[]" id="' . $name . '" class="gst_input_group_item ' . $class . '" value="' . $value . '">';
		$input_tag .= '<button title="' . $this->get_tooltip('delete_text_button'). '" class="gst-sort-edit-delete" id="' . $name . '-button" formaction="javascript:void(0)">'  . __('X', GUESTABA_CMP_TEXTDOMAIN ) . '</button>';
		$input_tag .= '</div>';
		return $input_tag;
	}

	/**
	 * Function item_as_image_upload
	 *
	 * This a callback function for a sortable-editable list. It returns the a string containing the HTML form elements
	 * required to add/edit slider images.
	 *
	 *
	 * @param $name
	 * @param $class
	 * @param $item
	 * @param $labels
	 *
	 * @return string
	 */
	protected function item_as_proposal_type( $name, $class, $item, $labels ) {



		$input_tag = '<div id="' . $name . '" class="gst-sort-edit-proposal-type gst_input_group">';
		$input_tag .= '<button title="' . $this->get_tooltip('delete_propsosal_type_button') . '" class="gst-sort-edit-delete" id="' . $name . '-button" formaction="javascript:void(0)">'  . __('X', GUESTABA_CMP_TEXTDOMAIN ) . '</button>';

		$input_tag .= '<br/><label class="gst_proposal_type_input_label gst_proposal_input_label_title" for="' . $name . '[title]" >' . $labels['title'] . '</label>';
		$input_tag .= '<input type="text"  name="'. $name . '[title]" class="gst_input_group_item gst_clear_input_target gst_proposal_type_input ' . $class . '" value="' . $item['title'] . '">';

		$input_tag .= '<br/><label class="gst_proposal_type_input_label gst_proposal_input_label_icon" for="' . $name . '[icon]" >' . $labels['icon'] . '</label>';
		$input_tag .= '<input type="text"  name="'. $name . '[icon]" class="gst_input_group_item gst_clear_input_target gst_proposal_type_input ' . $class . '" value="' . $item['icon'] . '">';

		if ( !isset( $item['marker_color'])) {
			$item['marker_color'] = '';
		}
		$input_tag .= '<br/><label class="gst_proposal_type_input_label gst_proposal_input_label_marker_color" for="' . $name . '[marker_color]" >' . $labels['marker_color'] . '</label>';
		$input_tag .= '<input type="text"  name="'. $name . '[marker_color]" class="gst-color-input gst_input_group_item gst_clear_input_target gst_proposal_type_input ' . $class . '" value="' . $item['marker_color'] . '" data-default-color="#007fef">';

		if ( !isset( $item['icon_color'])) {
			$item['icon_color'] = '';
		}
		$input_tag .= '<label class="gst_proposal_type_input_label gst_proposal_input_label_icon_color" for="' . $name . '[icon_color]" >' . $labels['icon_color'] . '</label>';
		$input_tag .= '<input type="text"  name="'. $name . '[icon_color]" class="gst-color-input gst_input_group_item gst_clear_input_target gst_proposal_type_input ' . $class . '" value="' . $item['icon_color'] . '" data-default-color="#ffffff">';


		$input_tag .= '</div>';
		return $input_tag;
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
			'meta_propsal_type_list' => __('Add proposal types in order to define the types of projects on which visitors will be asked to propose and comment.', GUESTABA_CMP_TEXTDOMAIN),
			'edit_image_button' => __( 'Click this button select or upload a different image. For best results, choose images 600 px wide by 150 px high.', GUESTABA_CMP_TEXTDOMAIN ),
			'delete_image_button' => __( 'Click this button to remove this image from the slider.', GUESTABA_CMP_TEXTDOMAIN ),
			'delete_text_button' => __( 'Click this button to remove this item from the list.', GUESTABA_CMP_TEXTDOMAIN ),
			'meta_map_detail_page' => __( 'Choose from this list only a custom page containing details for this map. Leave this unselected unless such a page has been set up for this map.', GUESTABA_CMP_TEXTDOMAIN ),
		    'meta_map_proposal_select' => __( 'Proposal models define how a map price varies based on things like seasons, upcoming events, and days of the week. They are set up in the Proposal Models post type page.', GUESTABA_CMP_TEXTDOMAIN ),
			'meta_map_center_lat' => __( 'This is the latitude for the map center.', GUESTABA_CMP_TEXTDOMAIN ),
			'meta_map_center_lng' => __( 'This is the longitude for the map center.', GUESTABA_CMP_TEXTDOMAIN ),
			'meta_map_sw_bnds_lat' =>  __( 'This is the latitude for the southwest bounds of the survey area.', GUESTABA_CMP_TEXTDOMAIN ),
			'meta_map_sw_bnds_lng' =>  __( 'This is the longitude for the southwest bounds of the survey area.', GUESTABA_CMP_TEXTDOMAIN ),
			'meta_map_ne_bnds_lat' =>  __( 'This is the latitude for the northeast bounds of the survey area.', GUESTABA_CMP_TEXTDOMAIN ),
			'meta_map_ne_bnds_lng' =>  __( 'This is the longitude for the northeast bounds of the survey area.', GUESTABA_CMP_TEXTDOMAIN ),
			'meta_map_zoom' =>  __( 'This is the initial zoom setting for the map.', GUESTABA_CMP_TEXTDOMAIN ),


		);

		$this->set_tooltips( $tooltips );
	}

	private function update_proposal_type_array( $post_id ) {

		if ( isset( $_POST['meta_proposal_type_list'] ) ) {

			$updates = $_POST['meta_proposal_type_list'];

			$clean_updates = array();

			foreach ( $updates as $update ) {
				$clean_updates[] = array(
					'title'  => sanitize_text_field( $update['title'] ),
					'icon' => sanitize_text_field( $update['icon'] ),
					'marker_color' => sanitize_text_field( $update['marker_color'] ),
					'icon_color' => sanitize_text_field( $update['icon_color'] ),
				);

			}

			update_post_meta( $post_id, 'meta_proposal_type_list', $clean_updates );
			$this->update_proposal_type_taxonomy( $clean_updates );
		}


	}

	private function update_proposal_type_taxonomy( $proposal_types ) {

		foreach ( $proposal_types as $proposal_type ) {
			$cat_name = $proposal_type['title'];
			$parent = 0;
			if ( $id = category_exists($cat_name, $parent) )
				continue ; // got it

			$args = array(
				'cat_name' => $cat_name,
				'category_parent' => $parent,
				'taxonomy' => 'proposal_type'
			);

			$ret_val = wp_insert_category( $args );

			if ( is_wp_error( $ret_val) ) {
				error_log( 'Error' .  __FILE__ . ':' . __LINE__ . ',' . $ret_val->get_error_message );
			}
			else if ( $ret_val === false ) {
				error_log( 'Error' .  __FILE__ . ':' . __LINE__ . ',' . __('Could not create proposal_type taxonomy member', GUESTABA_CMP_TEXTDOMAIN) );
			}


		}

	}

	private function update_survey_area_taxonomy ( $post_id ) {

		$sa_name = get_the_title( $post_id ) ;
		if ( $sa_name == 'Auto Draft' || empty( $sa_name ) ) {
			return; // new post, not yet titled.
		}
		$parent = 0;


		$id = term_exists($sa_name, 'survey_area', $parent);
		if ( is_array($id) )
			$id = $id['term_id'];


		if ( ! $id ) {
			$args = array(
				'cat_name' => $sa_name,
				'category_parent' => $parent,
				'taxonomy' => 'survey_area'
			);

			$id = wp_insert_category( $args, true );
		}


		if ( is_wp_error( $id ) ) {
			error_log( 'Error' .  __FILE__ . ':' . __LINE__ . ',' . $id->get_error_message );
			return;
		}
		else if ( $id === false ) {
			error_log( 'Error' .  __FILE__ . ':' . __LINE__ . ',' . __('Could not create proposal_type taxonomy member', GUESTABA_CMP_TEXTDOMAIN) );
			return;
		}

		$res = wp_set_object_terms( $post_id, intval($id ), 'survey_area');
		if( is_wp_error( $res )) {
			error_log('Error:' . __FILE__ . ',' . __LINE__  . ',' . __('Could not set survey_areas taxonomy', GUESTABA_CMP_TEXTDOMAIN));
		}

	}

	/*
	 * Function get_proposal_types
	 *
	 * @access private
	 *
	 * @param integer $post_ID the post ID of the current map CPT.
	 *
	 * @return array an array containing the proposal type data as returned by get_post_meta.
	 */

	private function get_proposal_types( $post_ID ) {
		$list = get_post_meta($post_ID, 'meta_proposal_type_list', true);

		if ( $list == false || !isset( $list) || empty($list)  ) {
			$list = array();
			$list[0] = array(   'title' => '',
								'icon' => '');
			return $list;
		}
		else {
			return $list;
		}

	}


}