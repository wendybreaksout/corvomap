<?php


/**
 * This class receives ajax requests for the plugin. 
 * 
 * @since      1.0.0
 * @package    CrowdMap
 * @subpackage CrowdMap/includes
 * @author     Wendy Emerson <wendybreaksout@gmail.com>
 */

class CrowdMap_Public_Ajax_Controller {
	
	/*
	 * Function: execute_request
	 * 
	 * This function is registered as the ajax responder for the
	 * plugin in Wordpress. It calls subordinate functions in order
	 * to satisfy the request. The return string from the subordinate
	 * function is output as a client response directly in this function.
	 * 
	 * If an exception is caught by this function, data related to the
	 * exception are formated and sent as an error response to the
	 * client. 
	 * 
	 * @param none directly. Reads $_REQUEST for 'fn' (function) parameter. 
	 * 
	 * Request currently processed: 
	 * get_slider_config, get_amenity_set_list, get_proposal.
	 * See corresponding functions in this class. 
	 * 
	 */
	
	public static function execute_request() {
		
		try {
			switch($_REQUEST['fn']){

				case 'publish_proposal':
					// If  not set, consider it an invalid request.
					if ( !isset( $_REQUEST['documentURL'] ) ) {
						throw new Exception(__('Invalid publish proposal request.', GUESTABA_CMP_TEXTDOMAIN ) );

					}
					$output = self::publish_proposal();
					if ( $output === false ) {
						throw new Exception(__('Error occurred attempting to publish proposal.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					break;


				case 'post_comment':
					// If  not set, consider it an invalid request.
					if ( !isset( $_REQUEST['documentURL'] )&& !isset( $_REQUEST['proposalID'] ) ) {
						throw new Exception(__('Invalid post comment request.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					$output = self::post_comment();
					if ( $output === false ) {
						throw new Exception(__('Error occurred attempting to post comment.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					break;

				case 'post_support':
					// If  not set, consider it an invalid request.
					if ( !isset( $_REQUEST['documentURL'] )&& !isset( $_REQUEST['proposalID'] ) ) {
						throw new Exception(__('Invalid post support request.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					$output = self::post_support();
					if ( $output === false ) {
						throw new Exception(__('Error occurred attempting to post support.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					break;

				case 'get_comments':
					// If  not set, consider it an invalid request.
					if ( !isset( $_REQUEST['documentURL'] )&& !isset( $_REQUEST['proposalID'] ) ) {
						throw new Exception(__('Invalid get comments request.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					$output = self::post_comment();
					if ( $output === false ) {
						throw new Exception(__('Error occurred attempting to get comments.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					break;

				case 'get_map_activity':
					// If  not set, consider it an invalid request.
					if ( !isset( $_REQUEST['documentURL'] )&& !isset( $_REQUEST['mapID'] ) ) {
						throw new Exception(__('Invalid get map activity request.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					$output = self::get_map_activity();
					if ( $output === false ) {
						throw new Exception(__('Error occurred attempting to get map activity.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					break;


				case 'get_geocoding' :
					// This case is a placeholder for now. Using wp_localize_script in class-crowdmap-public
					// to pass this information.
					// If  not set, consider it an invalid request.
					if ( !isset( $_REQUEST['documentURL'] )  ) {
						throw new Exception(__('Invalid maps slider request.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					$output = self::get_geocoding();
					if ( $output === false ) {
						throw new Exception(__('Could not geocoding for address.', GUESTABA_CMP_TEXTDOMAIN ) );
					}

					break;


				case 'get_proposal':
					// If  not set, consider it an invalid request.
					if ( !isset( $_REQUEST['documentURL']) || $_REQUEST['proposalID'] == 'undefined' ) {
						throw new Exception(__('Invalid get get proposal request.', GUESTABA_CMP_TEXTDOMAIN ) );
					}

					$output = self::get_proposal( );
					if ( $output === false || empty( $output) ) {
						throw new Exception(__('Could not get proposal with specified proposal ID.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					break;

				case 'get_proposals':
					// If  not set, consider it an invalid request.
					if ( !isset( $_REQUEST['mapID']) || $_REQUEST['mapID'] == 'undefined' ) {
						throw new Exception(__('Invalid get proposals request. No mapID.', GUESTABA_CMP_TEXTDOMAIN ) );
					}

					if ( !isset( $_REQUEST['since']) || $_REQUEST['since'] == 'undefined' ) {
						throw new Exception(__('Invalid get proposals request. The "since" param is not set', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					$output = self::get_proposals( $_REQUEST['mapID'], $_REQUEST['since']  );
					if ( $output === false ) {
						throw new Exception(__('Could not get proposals for specified map ID.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					break;

				case 'get_proposals_ranked':
					// If  not set, consider it an invalid request.
					if ( !isset( $_REQUEST['mapID']) || $_REQUEST['mapID'] == 'undefined' ) {
						throw new Exception(__('Invalid get proposals request. No mapID.', GUESTABA_CMP_TEXTDOMAIN ) );
					}

					$output = self::get_proposals_ranked( $_REQUEST['mapID'] );
					if ( $output === false ) {
						throw new Exception(__('Could not get proposals ranked by support for specified map ID.', GUESTABA_CMP_TEXTDOMAIN ) );
					}

					break;

				case 'get_proposals_export':
					// If  not set, consider it an invalid request.
					if ( !isset( $_REQUEST['mapID']) || $_REQUEST['mapID'] == 'undefined' ) {
						throw new Exception(__('Invalid get proposals request. No mapID.', GUESTABA_CMP_TEXTDOMAIN ) );
					}

					$output = self::get_proposals_export( $_REQUEST['mapID'] );
					if ( $output === false ) {
						throw new Exception(__('Could not get proposals ranked by support for specified map ID.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					/*
					 * Echo an exit here to skip JSON encoding. Header content type is
					 * set for CSV download.
					 *
					 */
					echo $output;
					exit;

					break;


				case 'get_post_edit_options':
					$output = self::get_post_edit_options();
					if ( $output === false || empty( $output) ) {
						throw new Exception(__('Could not get post edit options.', GUESTABA_CMP_TEXTDOMAIN ) );
					}
					break;
				default:
					$output = __('Unknown ajax request sent from client.', GUESTABA_CMP_TEXTDOMAIN );
					break;
	
			}
		} 
		catch ( Exception $e ) {
			$errorData = array(
				'errorData' => 'true',
				'errorMessage' => $e->getMessage(),
				'errorTrace' => $e->getTraceAsString()
			);
			$output = $errorData;
		}

		// Convert $output to JSON and echo it to the browser 
	
		$output=json_encode($output);
		if(is_array($output)){
			print_r($output);	
 		}
		else {
			echo  $output ;
	     }
		die;
	}


	/**
	 *
	 * Function: get_post_edit_options
	 *
	 * @return array returns options that are required for javascript configuration on client-side input elements.
	 */
	private static function get_post_edit_options() {

		$option = get_option( GUESTABA_CMP_OPTIONS_NAME );
		$post_edit_options = array();
		$post_edit_options['map_excerpt_max_char_count'] = $option['cmp_map_excerpt_len'];
		return $post_edit_options;
	}

	/**
	 *
	 * Function: get_geocoding
	 *
	 * @return array returns geo coding json string for address in plugin settings.
	 */
	private static function get_geocoding() {

		$option = get_option( GUESTABA_CMP_GEO_OPTIONS_NAME );
		$geocode_json = $option['geocode'] ;

		$geocode_array = json_decode( $geocode_json );

		return $geocode_array->results[0]->geometry->location ;

	}





	/*
	 * Get and return proposal for specified post ID.
	 * 
	 * @param string postID
	 * @return array containing proposal for specified maps post.
	 *
	 * @since 1.0.0
	 */
	private static function get_proposal() {

			$proposal_id = intval(( $_REQUEST['proposalID']));
			return Proposals_Post_Type::get_proposal( $proposal_id );
	}

	/*
		 * Get and return proposals for specified post (map) ID.
		 *
		 * @param string postID
		 * @return array containing proposal for specified maps post.
		 *
		 * @since 1.0.0
		 */
	private static function get_proposals() {

		$map_id = intval(( $_REQUEST['mapID']));
		$since = intval(( $_REQUEST['since']));

		return Maps_Post_Type::get_crowdmap_proposals( $map_id, $since );
	}


	/*
	 * Get and return proposals for specified post (map) ID ranked
	 * by support count.
	 *
	 * @param string postID
	 * @return array containing proposals for specified maps post.
	 *
	 * @since 1.0.0
	 */
	private static function get_proposals_ranked() {

		$map_id = intval(( $_REQUEST['mapID']));
		return Maps_Post_Type::get_crowdmap_proposals_ranked( $map_id );
	}

	private static function get_proposals_export() {

		$map_id = intval(( $_REQUEST['mapID']));
		$proposals =  Maps_Post_Type::get_crowdmap_proposals_ranked( $map_id );

		$s = 1;

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"proposals-map-" . $map_id .  ".csv\";" );
		header("Content-Transfer-Encoding: binary");

		$field_list = array('id', 'title', 'author','date', 'lat','lng', 'map_id', 'proposal_type','support_count','permalink');

		$output = '';
		$field_count = count($field_list);
		$count = 0;
		foreach ( $field_list as $field ) {
			$output .= $field;
			$count++;
			if ( $count < $field_count ) {
				$output .= ',';
			}
			else {
					$output .= "\n";
			}
		}

		foreach ( $proposals as $proposal ) {
			$field_count = count($field_list);
			$count = 0;
			foreach ( $field_list as $field ) {
				$output .= $proposal[ $field ];
				$count++;
				if ( $count < $field_count ) {
					$output .= ',';
				}
				else {
					$output .= "\n";
				}
			}
		}


		return $output ;
	}

	/*
	 * Get and return proposal for specified post ID.
	 *
	 * @param string postID
	 * @return array containing proposal for specified maps post.
	 *
	 * @since 1.0.0
	 */
	private static function publish_proposal() {

		/*
		 * This check is to see if lat/lng fields are floats. The values passed from
		 * the client will be stored as strings to preserve precision. The precision
		 * of 14 on the client side is truncated to 10 by float val.
		*/
		if ( floatval($_REQUEST['proposal']['lat']) == 0 ) return false;
		if ( floatval($_REQUEST['proposal']['lng']) == 0 ) return false;

		$proposal = array(
			'map_id' => wp_kses_post($_REQUEST['postID']),
			'lat' => $_REQUEST['proposal']['lat'],
			'lng' => $_REQUEST['proposal']['lng'],
			'title' => sanitize_text_field($_REQUEST['proposal']['title']),
			'description' => sanitize_text_field($_REQUEST['proposal']['description']),
			'proposal_type' => sanitize_text_field($_REQUEST['proposal']['proposal_type'])
		);

		return Proposals_Post_Type::publish_proposal( $proposal );
	}

	/*
	 * Post comment on proposal.
	 *
	 *
	 * @return true on success, false on error.
	 *
	 * @since 1.0.0
	 */
	private static function post_comment() {

		$comment = array(
			'proposal_id' => wp_kses_post($_REQUEST['proposalID']),
			'content' => sanitize_text_field($_REQUEST['content'])
		);

		return Proposals_Post_Type::post_comment( $comment );
	}

	/*
	 * Post comment on proposal.
	 *
	 *
	 * @return true on success, false on error.
	 *
	 * @since 1.0.0
	 */
	private static function post_support() {

		$comment = array(
			'proposal_id' => wp_kses_post($_REQUEST['proposalID']),
		);

		return Proposals_Post_Type::post_support( $comment );
	}


	/*
	 * Call proposal post type class to get array of map activities ordered by date.
	 */
	public static function get_map_activity( ) {

		$since = intval( $_REQUEST['since']);
		$map_id = intval( $_REQUEST['mapID']);
		return Maps_Post_Type::get_map_activity( $map_id, $since ) ;
	}

	/*
	 * Provides object access to exec request static function. 
	 * 
	 * @param none
	 * @return non
	 * @see execute_request()
	 */
	
	public function crowdmap_ajax() {
		self::execute_request();
	}
	
}


?>