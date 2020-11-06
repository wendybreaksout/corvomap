<?php
/**
 *
 */

abstract class CrowdMap_Meta_Box {


	/**
	 * @var string post_type, used in WP API calls that require it.
	 */
	private $post_type ;

	/**
	 * @var string meta_box_ID, unique ID of the metabox, for reference by WP
	 */
	private $meta_box_ID;

	/**
	 * @var string meta_box_title, display in dashboard as the title.
	 */
	private $meta_box_title ;

	/**
	 * @var string nonce_id, storage for form submission nonce.
	 */
	private $nonce_id ;

	/**
	 * @var array tooltips,  Initialized from child/concrete class with setToolTips
	 */
	private $tooltips ;

	/**
	 * @var string nothing_selected, used to indicated nothing selected in drop down lists.
	 * Initialized to "--Choose One--" or the localized translation thereof in constructor.
	 */
	protected $nothing_selected ;

	/**
	 * Class constructor
	 */
	public function __constructor() {
		$this->nothing_selected = __('-- Choose one --', GUESTABA_CMP_TEXTDOMAIN );

	}

	// public abstract function meta_box_init() ;

	/**
	 * Function meta_box_render
	 *
	 * This function must be defined in the child class. This is the function that
	 * renders the metabox form elements.
	 *
	 * @return void
	 */
	public abstract function meta_box_render();

	/**
	 *
	 * Function post_meta_save
	 *
	 * This function must be defined in the sub class. It saves form values on submission.
	 * @param $post_id
	 *
	 * @return void
	 */
	public abstract function post_meta_save( $post_id );

	/**
	 *
	 * Function init_tooltips
	 *
	 * This function must be defined in the sub class. It intializes the associative array where
	 * tooltips are stored.
	 *
	 * @return void
	 */
	protected abstract function init_tooltips();

	/**
	 *
	 * Function getPostType, returns the post type with which this metabox is associated.
	 * @return string
	 */
	public function getPostType() {
		return $this->post_type;
	}

	/**
	 *
	 * Function setPostType, set the post type with which the metabox is assoicated.
	 * @param string $post_type
	 */
	public function setPostType( $post_type ) {
		$this->post_type = $post_type;
	}

	/**
	 *
	 * Function getMetaBoxID, returns the metabox ID.
	 *
	 * @return string
	 */
	public function getMetaBoxID() {
		return $this->meta_box_ID;
	}

	/**
	 * Function setMetaBoxID, set metabox ID.
	 * @param mixed $meta_box_ID
	 */
	public function setMetaBoxID( $meta_box_ID ) {
		$this->meta_box_ID = $meta_box_ID;
	}

	/**
	 *
	 * Function getMetaBoxTitle()
	 *
	 * Returns metabox title.
	 *
	 * @return string
	 */
	public function getMetaBoxTitle() {
		return $this->meta_box_title;
	}

	/**
	 * Function setMetaBoxTitle
	 *
	 * Sets the title string for the metabox.
	 *
	 * @param string $meta_box_title
	 */
	public function setMetaBoxTitle( $meta_box_title ) {
		$this->meta_box_title = $meta_box_title;
	}

	/**
	 *
	 * Function getNonceId
	 *
	 * Returns the nonce ID.
	 *
	 * @return string
	 */
	public function getNonceId() {
		return $this->nonce_id;
	}

	/**
	 *
	 * Function setNonceId
	 *
	 * Sets the nonce ID.
	 *
	 * @param mixed $nonce_id
	 */
	public function setNonceId( $nonce_id ) {
		$this->nonce_id = $nonce_id;
	}


	/**
	 * Function meta_box_init
	 *
	 * This is the callback function to be specified when calling add_action() on the 'add_meta_boxes' hook.
	 *
	 * @return void
	 */
	public function meta_box_init() {

		add_meta_box( $this->meta_box_ID, $this->meta_box_title , array( $this, 'meta_box_render'), $this->post_type );
		add_action('save_post', array( $this, 'post_meta_save'));

	}

	/**
	 * Function update_meta_text
	 *
	 * Sanitizes text value from form input and calls update_post_meta to save post meta data.
	 * Normally called from subclass post_meta_save function.
	 *
	 * @param integer $post_id the post ID for the current post.
	 * @param  string $name name of the $_POST element from which to retrieve the value.
	 */
	protected function update_meta_text( $post_id, $name ) {

		$name = trim( $name );
		if ( isset( $_POST[ $name ] ) ) {
			update_post_meta( $post_id, $name, sanitize_text_field( $_POST[ $name ] ) );
		}
	}

	/**
	 * Function update_meta_text
	 *
	 * Sanitizes text value from form input and calls update_post_meta to save post meta data.
	 * Normally called from subclass post_meta_save function.
	 *
	 * @param integer $post_id the post ID for the current post.
	 * @param  string $name name of the $_POST element from which to retrieve the value.
	 */
	protected function update_meta_text_html( $post_id, $name ) {

		$name = trim( $name );
		if ( isset( $_POST[ $name ] ) ) {
			update_post_meta( $post_id, $name, wp_kses_post( $_POST[ $name ] ) );
		}
	}

	/**
	 * Function update_meta_integer
	 *
	 * 	Sanitizes integer value from form input and calls update_post_meta to save post meta data.
	 *  Normally called from subclass post_meta_save function.
	 *
	 * @param integer $post_id the post ID for the current post.
	 * @param integer $name name of the $_POST element from which to retrieve the value.

	 */
	protected function update_meta_integer( $post_id, $name ) {
		if ( isset( $_POST[ $name ] ) ) {
			update_post_meta( $post_id, $name, intval( $_POST[ $name ] ) );
		}
	}



	/**
	 * Function update_meta_integer
	 *
	 * 	Sanitizes integer value from form input and calls update_post_meta to save post meta data.
	 *  Normally called from subclass post_meta_save function.
	 *
	 * @param integer $post_id the post ID for the current post.
	 * @param integer $name name of the $_POST element from which to retrieve the value.

	 */
	protected function update_meta_float( $post_id, $name ) {
		if ( isset( $_POST[ $name ] ) ) {
			update_post_meta( $post_id, $name, floatval( $_POST[ $name ] ) );
		}
	}

	/**
	 * Function update_meta_post_reference
	 *
	 * This function is used for fields that contains references (by post_id) to other posts.
	 * It first verifies that post exists and then calls update_post_meta.
	 * Normally called from subclass post_meta_save function.
	 *
	 * @todo There is no error handling for the case in which the input post ID is found not to exist.
	 *
	 * @param integer $post_id the referenced post ID
	 * @param string $name the name of the $_POST element from which the reference is retrieved.
	 * @param string $post_type the post type of the reference post. Defaults to 'post'.
	 */
	protected function update_meta_post_reference( $post_id, $name, $post_type = 'post' ) {

		if ( isset($_POST[ $name ]) ) {
			$ref_id = intval( $_POST[ $name ] );

			$args = array(
				'id'             => $ref_id,
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => 1
			);

			$query = new WP_Query( $args );
			if ( $query->have_posts() ) {
				update_post_meta( $post_id, $name, $ref_id );
			}

			wp_reset_postdata();
		}

	}

	/**
	 * Function update_meta_page_reference
	 *
	 * This function is used for fields that contains references (by post_id) pages.
	 * It first verifies that post exists and then calls update_post_meta.
	 * Normally called from subclass post_meta_save function.
	 *
	 * @todo There is no error handling for the case in which the input page ID is found not to exist.
	 *
	 * @param integer $post_id the referenced page ID
	 * @param string $name the name of the $_POST element from which the reference is retrieved.
	 */
	protected function update_meta_page_reference( $post_id, $name ) {

		if ( isset($_POST[ $name ]) ) {
			$ref_id = intval( $_POST[ $name ] );
			$query  = new WP_Query( 'page_id=' . $ref_id );
			if ( $query->have_posts() ) {
				update_post_meta( $post_id, $name, $ref_id );
			}

			wp_reset_postdata();
		}
	}

	/**
	 * Function update_meta_array
	 *
	 * Sanatize and update post meta array values.
	 * Normally called from subclass post_meta_save function.
	 *
	 * @param integer $post_id the referenced page ID
	 * @param string $name name of the $_POST element from which to retrieve the values.
	 * @param array $fields associative index(es) of elements containing updated values.
	 * @param callback $validate_item, a function to validate each item in the array.
	 */
	protected function update_meta_array( $post_id, $name, $fields, $validate_item = "" ) {

		$verify_field = $name . '-' . $fields[0];

		if ( isset($_POST[ $verify_field ]) ) {
			// get item count the first field array. Assume they'll all be the same
			$item_count = count( $_POST[ $verify_field ] );
			$updates    = array();

			for ( $i = 0; $i < $item_count; $i ++ ) {
				$item = array();
				foreach ( $fields as $field ) {
					$item [ $field ] = sanitize_text_field( $_POST[ $name . '-' . $field ][ $i ] );
				}

				// call validation function if specified. Include in update if valid.
				$args = array();
				$args[] = $item;
				if ( !empty( $validate_item ) ) {
					if ( $this->exec_callback( $validate_item, $args )) {
						$updates[] = $item;
					}

				}
				else {
					$updates[] = $item;
				}

			}
			update_post_meta( $post_id, $name, $updates );
		}


	}



	/**
	 * Function section_heading
	 *
	 * Renders section heading. Normally called from subclass meta_box_render() function.
	 *
	 * @param string $content heading label content.
	 * @param string $for name of form element with which label will be associated.
	 */
	protected function section_heading( $content, $for ) {
		$heading_tag = '<div class="gst_mb_section gst_mb">';
		$heading_tag .= '<label for="' . $for . '" class="label">' . $content . '</label>';
		$heading_tag .= '</div>';
		echo $heading_tag;
	}

	/**
	 * Function text_input
	 *
	 * Renders an input field of type text.  Normally called from subclass meta_box_render() function.
	 *
	 * @param string $label the label for the text field.
	 * @param string $content the current value of the field.
	 * @param string $name the field name
	 * @param string $default (optional) the default value. If content is empty, the field will be set to the value of the default parameter.
	 */
	protected function text_input( $label, $content, $name, $default = '' ) {

		if ( !isset( $content ) || empty( $content )) {
			$content = $default;
		}

		$this->label( $label, $name );

		$input_tag = '<div title="' . $this->get_tooltip( $name ) . '" class="gst_mb_textinput">';
		$input_tag .= '<input type="text" name="' . $name . '" id="' . $name . '" value="' . $content . '">';
		$input_tag .= '</div>';
		echo $input_tag;

	}

	/**
	 *
	 * Function number_input
	 *
	 * Render an input element of type number.  Normally called from subclass meta_box_render() function.
	 *
	 * @param string $label the label for the number input field.
	 * @param string $content the current value of the field.
	 * @param string $name the field name
	 * @param string $default (optional) the default value. If content is empty, the field will be set to the value of the default parameter.
	 */
	protected function number_input( $label, $content, $name, $default ='', $validator = '') {

		if ( !isset( $content ) || empty( $content )) {
			$content = $default;
		}

		$validation = '';
		if ( $validator != '') {
			$validation = 'min="' . $validator['min'] . '" max="' . $validator['max'] . '" ' . 'step="' . $validator['step'] . '"';
		}

		$this->label( $label, $name );

		$input_tag = '<div title="' . $this->get_tooltip( $name ) . '" class="gst_mb_numberinput">';
		$input_tag .= '<input type="number" name="' . $name . '" id="' . $name . '" value="' . $content . '" ' . $validation .  '">';
		$input_tag .= '</div>';
		echo $input_tag;

	}

	/**
	 * Function text_area
	 *
	 * Renders a textarea input element. This function is normally called from the meta_box_render() function in the sub class.
	 *
	 * @param string $label the label for the field.
	 * @param string $content the current content of the field.
	 * @param integer $rows the number of rows (height) of the textarea.
	 * @param integer $columns the number of character columns (width) of the textarea.
	 * @param string $name the name of field.
	 * @param string $default (optional) the default value of the field if content is empty.
	 * @param boolean $countable
	 * @param boolean $visual_editor default falsel
	 */
	protected function text_area( $label, $content, $rows, $columns, $name, $default = '', $countable = false, $field_class = '' ) {

		if ( !isset( $content ) || empty( $content )) {
			$content = $default;
		}

		$this->label( $label, $name );

		$input_tag = '<div title="' . $this->get_tooltip( $name ) . '"  class="gst_mb_textarea">';

		$input_tag .= '<textarea class="textarea gst_mb_textarea_input ' . $field_class . ( $countable ? ' gst_countable' : '' ) . ' " rows="' . $rows . '" cols="' . $columns . '" name="' . $name . '" id="' . $name . '">' . $content . '</textarea>';
		if ( $countable ) {
			$input_tag .= '<p>Characters remaining: <span id="gst_counter"></span></p>';
		}
		$input_tag .= '</div>';
		echo $input_tag;


	}

	/**
	 * Function radio
	 *
	 * Render an input element of type radio. Normally, this function is called from the meta_box_render function in the sub class.
	 *
	 * @param string $label the label for the field.
	 * @param string $current_value the current value of the field.
	 * @param array $choices a list of radio button choices.
	 * @param string $name the name of the field.
	 * @param string $default the default value of the field if current value is not set.
	 */
	protected function radio( $label, $current_value, $choices, $name, $default = '' ) {

		if ( !isset( $current_value ) || empty( $current_value )) {
			$current_value = $default;
		}

		$this->label( $label, $name );

		$input_tag = '<div title="' . $this->get_tooltip( $name ) . '" class="gst_mb_radio">';

		foreach ( $choices as $choice ) {
			$input_tag .= '<input type="radio" name="' . $name . '" value="' . $choice['value'] . '" ';
			$input_tag .= ( $choice['value'] == $current_value ) ? 'checked="checked"' : '';
			$input_tag .= '>' . $choice['label'] . '&nbsp';
		}
		$input_tag .= '</div>';

		echo $input_tag;
	}

	/**
	 * Function on_off
	 *
	 * Renders a radio button with the choices 'on' and 'off'.
	 *
	 * @param string $label the label for the input field.
	 * @param string $current_value the current value of the field.
	 * @param string $name the name of the field.
	 * @param string $default the default value if content is not set.
	 */
	protected function on_off( $label, $current_value, $name, $default = '' ) {
		$choices[0] = array( 'label' => 'On', 'value' => 'on' );
		$choices[1] = array( 'label' => 'Off', 'value' => 'off' );
		$this->radio( $label, $current_value, $choices, $name, $default );
	}

	/**
	 * Function page_select
	 *
	 * Renders a select element with a list of all pages (which are listed by title) as options.
	 *
	 * @param string $label the label for the field.
	 * @param string $selection the current selection.
	 * @param string $name the name of the field.
	 */
	protected function page_select( $label, $selection, $name ) {


		$this->label( $label, $name );
		$select_tag = '<div title="' . $this->get_tooltip( $name ) . '" class="gst_mb_select">';
		$select_tag .= '<select name="' . $name . '">';

		$select_tag .= $this->null_option();

		$pages = get_pages();
		foreach ( $pages as $page ) {
			$option = '<option value="' . $page->ID . '" ';
			if ( $page->ID == $selection ) {
				$option .= 'selected="selected" ';
			}
			$option .= '>';
			$option .= $page->post_title;

			$option .= '</option>';
			$select_tag .= $option;
		}
		$select_tag .= '</select>';
		$select_tag .= '</div>';

		echo $select_tag;

	}

	/**
	 * Function post_select
	 *
	 * This function renders a select list of posts (listed by title).
	 *
	 * @param string $label the label for the field.
	 * @param string $selection the current selection.
	 * @param string $name, the name of the field
	 * @param string $post_type the post type to list. Defaults to 'post'.
	 * @param string $class a user defined class for the generated select element.
	 */
	protected function post_select( $label, $selection, $name, $post_type, $class = "" ) {

		$args = array(
			'post_type'      => isset( $post_type ) ? $post_type : 'post',
			'post_status'    => 'publish',
			'posts_per_page' => - 1
		);

		$post_query = new WP_Query( $args );

		$this->label( $label, $name );

		echo '<div title="' . $this->get_tooltip( $name ) . '" class="gst_mb_select">';
		$select_tag = '<select name="' . $name . '" ';
		$select_tag .= 'id="' . $name . '" ';
		$select_tag .= 'class="' . $class . '" ';
		$select_tag .= '>';


		$select_tag .= $this->null_option();

		while ( $post_query->have_posts() ) : $post_query->the_post();
			$id     = get_the_ID();
			$title  = get_the_title();
			$option = '<option value="' . $id . '" ';
			if ( $id == $selection ) {
				$option .= 'selected="selected" ';
			}
			$option .= '>';
			$option .= $title;
			$option .= '</option>';
			$select_tag .= $option;

		endwhile;

		wp_reset_postdata();

		$select_tag .= '</select>';
		$select_tag .= '</div>';


		echo $select_tag;


	}

	/**
	 *  Function select
	 *
	 *  Renders a select element.
	 *
	 * @param string $label the label for the field.
	 * @param string $selection the current selection for the field.
	 * @param array $items the items from which to generate the select options. Each item should
	 * be an associative array with elements 'value' and 'label'.
	 * @param string $name the name of the field.
	 * @param string $default the default value if selection is empty.
	 */
	protected function select( $label, $selection, $items, $name, $default = '' ) {

		if ( !isset($selection) || empty( $selection) ) {
			$selection = $default;
		}

		$this->label( $label, $name );
		$select_tag = '<div title="' . $this->get_tooltip( $name ) . '" class="gst_mb_select">';
		$select_tag .= '<select name="' . $name . '">';

		$select_tag .= $this->null_option();

		foreach ( $items as $item ) {
			$option = '<option ';
			if ( $item['value'] == $selection ) {
				$option .= ' selected ';
			}
			$option .= 'value="' . $item['value'] . '">';
			$option .= $item['label'];

			$option .= '</option>';
			$select_tag .= $option;
		}
		$select_tag .= '</select>';
		$select_tag .= '</div>';

		echo $select_tag;

	}


	/**
	 * Function sortable_editable_list
	 *
	 * Renders a set of input objects that can can be sorted by the user.
	 *
	 * @todo no error handling for the case in which exec_callback returns FALSE.
	 *
	 * @param string $label the label for the field.
	 * @param array $items a post meta data array, as returned from get_post_meta()
	 * @param string $name the name of the field.
	 * @param string $item_callback a call back function which generates the HTML for the list items. This function has
	 * no visibility into inner HTML of the items it manages.
	 * @param string $class user-defined class.
	 * @param array|string $labels an array of labels for each input element contained in the list object.
	 * @param string $add_button_class the name of the class for the "add" button. Defaults to 'gst-sort-edit-add'.
	 * @param bool $index_name specifies whether or not to add index to name for each items (e.g. somename[0])
	 *
	 */
	protected function sortable_editable_list(  $label,
												$items,
												$name,
												$item_callback = '',
												$class = '',
												$labels = '',
												$add_button_class = 'gst-sort-edit-add',
												$index_name = false
												)
	{

		$name = trim( $name );

		$this->label( $label, $name );

		echo '<div class="' . $name . '_feedback"></div>';
		echo '<div title="' . $this->get_tooltip( $name ) . '" class="gst_mb_sort_edit">';
		$ul = '<ul name="' . $name . '" ';
		$ul .= 'id="' . $name . '" ';
		$ul .= 'class="gst-sortable ' . $class . ' "';
		$ul .= '>';

		$item_class = $name . "_list_item";

		$i = 0;
		$li = '';
		foreach ( $items as $item ) {
			$li = '<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>';


			$args = array();
			$item_content = '';
			if ( ! empty( $item_callback ) ) {
				// $args[] =  $name . '-' . $i ;
				$args[]       = $name . ( $index_name ? '[' . $i . ']' : '' );
				$args[]       = $item_class;
				$args[]       = $item;
				$args[]       = $labels;
				$item_content = $this->exec_callback( $item_callback, $args );
			}

			$li .= $item_content;

			$li .= '</li>';


			$ul .= $li;
			$i ++;
		}

		// Serialize last list item and place in hidden form field. To be used on client side to create new items.

		$li_encoded = base64_encode( $li );

		$ul .= '</ul>';
		$ul .= '<div class="gst-sort-edit-add-container">';
		$ul .= '<button type="button" title="' . $this->get_tooltip( 'add_button' ) . '" class="' . $add_button_class . '" id="' . $name . ' add-button" formaction="javascript:void(0)">' . __( '+', GUESTABA_CMP_TEXTDOMAIN ) . '</button>';
		$ul .= '<input type="hidden" id="'. $name . '-template" name="'. $name . '-template" class="gst_clone_template" value="'. $li_encoded . '">';
		$ul .= '</div>';
		$ul .= '</div>';

		echo $ul;


	}

	/**
	 * Function label
	 *
	 * Renders a label. This is typically called by other functions of this class.
	 * @param string $label the label content.
	 * @param string $name the name of the form element with which the label is associated. Used in the "for" attribute
	 * of the <label> tag.
	 */
	protected function label( $label, $name ) {

		$label_tag = '<div class="gst_mb_label">';
		$label_tag .= '<label for"' . $name . '" class="label">' . $label . '</label>';
		$label_tag .= '</div>';
		echo $label_tag;
	}

	/**
	 *
	 * Function null_option
	 *
	 * Returns an option to be used in a select for the purpose of indicating no selection. Called select generating functions
	 * in this class.
	 * @return string
	 */
	protected function null_option() {
		$option_tag = '<option value="">' .  __('--Choose One--', GUESTABA_CMP_TEXTDOMAIN ) . '</option>';
		return $option_tag;
	}

	/**
	 * Function content_container
	 *
	 * Renders empty div element which can be subsequently populated with javascript on the client side.
	 *
	 * @param string $class a class name for the element.
	 */
	protected function content_container( $class ) {
		$div_tag = '<div class="' . $class . '"></div>';
		echo $div_tag;

	}

	/**
	 * Function exec_callback
	 *
	 * Called by sortable_editable_list() function to execute the list item callback. The expected
	 * return from the callback is a string containing the HTML which defines the item input elements.
	 *
	 * @param string $callback the name of the callback function.
	 * @param array $args an array of arguments for the callback function.
	 *
	 * @return string containing HTML for sort/edit list object. Returns FALSE if the supplied callback function
	 * is not callable as determined by is_callable().
	 */
	protected function exec_callback( $callback, $args ) {
		if ( is_callable( $callback ) ) {
			return call_user_func_array( $callback, $args );
		}
		return FALSE;
	}

	/**
	 * Function set_tooltips
	 *
	 * Sets the tooltip values for the metabox. The tooltips parameter should be an associative array indexed
	 * by the name of each input element in the metabox. The value contained in each element is the tooltip for its
	 * respective input field.
	 *
	 * @param array $tooltips an associative array of tooltips indexed by field names.
	 */
	protected function set_tooltips ( $tooltips) {
		$this->tooltips = $tooltips;
	}

	/**
	 * Function get_tooltip
	 *
	 * Returns the respective tooltip for the metabox form element as specified in the name parameter.
	 *
	 * @param string $name the name of the form element.
	 *
	 * @return string the tooltip for the named element.
	 */
	protected function get_tooltip( $name ) {
		if ( isset( $this->tooltips[ $name ]  ))
			return  $this->tooltips[ $name ]  ;
		else
			return '';
	}

}