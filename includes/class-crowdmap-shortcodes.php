<?php

/**
 * This class defines and maintains the all plugin shortcodes which are:
 *
 * 	proposals -- list proposal for current or specified map post
 * 	map_images -- display images/sliders for current of specified map post
 *  map_slogan -- display the map slogan
 *  map_desc -- display the map description
 *  map_thumbnail -- display the map thumbnail (derived from featured image)
 *  map_price_range -- display map price range (min and max from proposal)
 *  map_listing -- will display a full listing of the current or specified map.
 * 
 * @link       http://guestaba.com
 * @since      1.0.0
 * @package    CrowdMap
 * @subpackage CrowdMap/includes
 * @author     Wes Kempfer <wkempferjr@tnotw.com>
 */
class CrowdMap_Shortcodes {

	/* 
	 * Used to store an instance of Hospitalty_Settings
	 */
	private $settings;

	/*
	 * A list of all of the shortcodes.
	 */
	private static $shortcodes = array(
		'map_desc',
		'map_thumbnail',
		'map_listing',
		'map_detail',
		'map_detail_link',
		'location_map',
		'corvomap',
		'map_activity'
	);


	/*
	 * Constructor
	 */

	public function __construct() {
		$this->settings = new CrowdMap_Settings();
	}

	/*
	 * Called to register all shortcodes for the plugin
	 * @since 1.0.0
	 */

	public function register_shortcodes() {

		foreach ( self::$shortcodes as $shortcode ) {
			add_shortcode( $shortcode, array( $this, $shortcode ) );
		}

	}

	/*
	 * Function: get_shortcodes
	 * @return array shortcodes an array of the plugin shortcode names.
	 */

	public static function get_shortcodes() {
		return self::$shortcodes;
	}



	/*
	 * Define the map detail link shortcode.
	 * @since 1.0.0
	 * @param array $atts
	 * @return string shortcode output.
	 */
	public function map_detail_link( $atts ) {

		$default_id = get_the_ID();

		/** @var $link_text string */
		/** @var $link_class */
		/** @var $id string */

		$atts_actual = shortcode_atts(
			array(
				'link_text'  => __( '[See details]', GUESTABA_CMP_TEXTDOMAIN ),
				'link_class' => 'map-detail-link',
				'id'         => $default_id
			),
			$atts );

		extract( $atts_actual );

		global $post;

		$map_detail_page = $this->get_map_detail_url( $id );

		if ( $map_detail_page == false ) {
			$map_detail_page = "#";
			error_log( __FILE__ . ", line number:" . __LINE__ . " Could not get map detail URL." );
		}

		$target_post = get_post( $id );
		$output      = '<a class="' . $link_class . '" href="' . $map_detail_page . '" title="' . $target_post->post_name . '">' . $link_text . '</a>';


		return $output;
	}


	/*
	 * Define the map description shortcode.
	 * @since 1.0.0
	 * @param array $atts
	 * @return string shortcode output.
	 */
	public function map_desc( $atts ) {

		$default_id = get_the_ID();


		/** @var $map_desc_class string */
		/** @var $more_anchor_class string */
		/** @var $more_anchor_title string */
		/** @var $more_text string */
		/** @var $id string */
		/** @var $excerpt_length string */
		$atts_actual = shortcode_atts(
			array(
				'map_desc_class'   => 'map-desc',
				'more_anchor_class' => 'maps_more_details',
				'more_anchor_title' => __( 'Click here for all map details.', GUESTABA_CMP_TEXTDOMAIN ),
				'more_text'         => __( '...Map Details', GUESTABA_CMP_TEXTDOMAIN ),
				'id'                => $default_id,
				'excerpt_length'    => - 1
			),
			$atts );

		extract( $atts_actual );

		$map_desc = get_post_meta( $id, 'meta_map_desc', true );

		$output = '';
		if ( ! empty( $map_desc ) ) {

			global $post;

			if ( $_SERVER['REQUEST_URI'] == '/' . GUESTABA_MAPS_LISTING_PAGE_NAME . '/' ) {
				$map_detail_page = get_site_url() . '/' . GUESTABA_MAP_DETAIL_PAGE_NAME . '/' . $post->post_name . '/';
			} else {

				$map_detail_page_id = get_post_meta( $id, 'meta_map_detail_page', true );
				if ( empty( $map_detail_page_id ) ) {
					$map_detail_page = get_post_permalink();
				} else {
					$map_detail_page = get_post_permalink( $map_detail_page_id );
				}
			}


			/** @todo This code might support word-based excerpt maxiumum. Remove if feature is not requested within a few months. */
			if ( $excerpt_length > 0 ) {
				$max_desc_word_count = $excerpt_length;
				$more_link           = '<a class="' . $more_anchor_class . '" href="' . $map_detail_page . '" title="' . $more_anchor_title . '">' . $more_text . '</a>';
				$word_count          = str_word_count( strip_tags( $map_desc ) );
				if ( $word_count > $max_desc_word_count ) {
					$trimmed_desc = wp_trim_words( $map_desc, $max_desc_word_count, $more_link );
				} else {
					$trimmed_desc = $map_desc . $more_link;
				}
				$map_desc = $trimmed_desc;
			}
			$output = '<p class="' . $map_desc_class . '">' . $map_desc . '</p>';


		}

		return $output;
	}

	/*
	 * Define the map thumbnail shortcode.
	 * @since 1.0.0
	 * @param array $atts
	 * @return string shortcode output.
	 */
	public function map_listing( $atts ) {

		$default_id = get_the_ID();

		/** @var  $title */
		/** @var  $id */
		/** @var  $header_tag */
		/** @var  $entry_tag */
		$atts_actual = shortcode_atts(
			array(
				'title'      => __( 'Maps & Rates', GUESTABA_CMP_TEXTDOMAIN ),
				'header_tag' => 'h2',
				'entry_tag'  => 'h3',
				'id'         => $default_id
			),
			$atts );

		extract( $atts_actual );

		$args = array(
			'post_type'      => 'maps',
			'post_status'    => 'publish',
			'posts_per_page' => - 1
		);

		$rm_query = new WP_Query( $args );

		$output = "";
		if ( $rm_query->have_posts() ) {
			$output = '<div id="maps_rates">';
			$output .= '<header class="maps-rates-header entry-header">';
			if ( ! empty( $title ) ) {
				$output .= '<' . $header_tag . ' class="maps-rates-title entry-title">';
				$output .= $title;
				$output .= '</' . $header_tag . '>';
			}
			$output .= '</header>';

			$output .= '<div class="row" data-equalizer="card-section-eq">';

			while ( $rm_query->have_posts() ) : $rm_query->the_post();

				$output .= '<div class="small-12 medium-4 large-4 columns">';
				$output .= '<div class="card">';
				$output .= do_shortcode( '[map_thumbnail]' );
				$output .= '<div class="card-divider">';
				$output .= do_shortcode( '[map_price_range label=""]' );
				$output .= '</div>';
				$output .= '<div class="card-section" data-equalizer-watch="card-section-eq">';
				$output .= '<' . $entry_tag . '>' . get_the_title() . '</' . $entry_tag . '>';
				$output .= do_shortcode( '[map_excerpt]' );
				$output .= '</div>';

				$output .= '</div>'; // end card
				$output .= '</div>'; // end column

			endwhile;

			$output .= '</div>'; // end row


			$output .= '</div>';

		}
		wp_reset_postdata();

		return $output;


	}

	public function map_thumbnail( $atts ) {

		$default_id = get_the_ID();

		/** @var  $id */
		/** @var $thumbnail_anchor_class */
		/** @var $thumbnail_image_class */
		/** @var  $size */

		$atts_actual = shortcode_atts(
			array(
				'thumbnail_anchor_class' => 'maps_img_link',
				'thumbnail_image_class'  => 'map-thumbnail',
				'size'                   => array( 200, 200 ),
				'id'                     => $default_id
			),
			$atts );

		extract( $atts_actual );

		$tn_output = '';
		/** TODO: add fancy box functionality */
		if ( has_post_thumbnail( $id ) ) {
			$post_thumbnail_id  = get_post_thumbnail_id( $id );
			$post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );

			$large_image_url = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
			$tn_output .= '<a class="' . $atts_actual['thumbnail_anchor_class'] . '" href="' . $this->get_map_detail_url( $id ) . '" title="' . the_title_attribute( 'echo=0' ) . '">';
			// $tn_output .= get_the_post_thumbnail( $id, $atts_actual['size'] , array( 'class' => $thumbnail_image_class ) );
			$tn_output .= '<img title="image title" alt="thumb image" class="wp-post-image ' . $thumbnail_image_class . '" src="' . $post_thumbnail_url . '">';
			$tn_output .= '</a>';
		}

		return $tn_output;
	}



	/*
	 * Define the map listing shortcode. Displays all maps
	 * as specified by current settings.
	 *
	 * @since 1.0.0
	 * @param array $atts
	 * @return string shortcode output.
	 */

	public function map_detail( $atts ) {


		/** @var  $id */
		/** @var $map_title_tag */
		/** @var  $map_detail_title */

		$atts_actual = shortcode_atts(
			array(
				'id'                => '',
				'map_title_tag'    => 'h3',
				'map_detail_title' => __( 'Back to map listing', GUESTABA_CMP_TEXTDOMAIN )
			),
			$atts );


		extract( $atts_actual );

		$output = '<div id="map-listing" class="sticky">';
		$output = '<' . $map_title_tag . ' id="map-post-title" >' . get_the_title( $id ) . '</' . $map_title_tag . '>' . '<a href="' . get_site_url() . '/' . GUESTABA_MAPS_LISTING_PAGE_NAME . '/" title="Back to Map Listings" class="back-link" ' . $map_detail_title . '">' . $map_detail_title . '</a>';

		$output .= '<div id="map_desc_container">';
		$output .= do_shortcode( '[map_desc id="' . $id . '"]' );
		$output .= '</div>';

		$output .= do_shortcode( '[corvomap id="' . $id . '"]' );

		$output .= '</div>'; /* end of listing container */
		// $output .= do_shortcode( '[map_activity id="' . $id . '"]' );

		return $output;
	}

	/**
	 *
	 * Function: get_map_detail_url
	 *
	 * @return string the URL for the current map.
	 */

	private function get_map_detail_url( $id ) {


		$override_detail_page = get_post_meta( $id, 'meta_map_detail_page', true );

		if ( ! isset( $override_detail_page ) || empty( $override_detail_page ) || $override_detail_page == 0 ) {
			$target_post      = get_post( $id );
			$map_detail_page = get_site_url() . '/' . GUESTABA_MAP_DETAIL_PAGE_NAME . '/' . $target_post->post_name . '/';

		} else {
			$map_detail_page = get_permalink( $override_detail_page );
		}

		return $map_detail_page;
	}

	/*
	 * Location map shortcode. Displays map of facility location.
	 *
	 * @since 1.0.4
	 * @param array $atts
	 * @return string shortcode output.
	 */
	public function location_map( $atts ) {

		$default_id = get_the_ID();

		$options = get_option( GUESTABA_CMP_OPTIONS_NAME) ;
		$default_zoom = $options['cmp_google_maps_default_zoom'];
		$location_address = $options['cmp_street_address_1'] ;
		if ( strlen( $options['cmp_street_address_2']) > 0 ) {
			$location_address .= ', ' . $options['cmp_street_address_2'];
		}
		$location_address .= '<br>' . $options['cmp_city'] ;
		$location_address .= ', ' . $options['cmp_state'];
		$location_address .= ' ' . $options['cmp_postal_code'];

		/** @var  $id */
		/** @var $zoom */
		/** @var $title */
		$atts_actual = shortcode_atts(
			array(
				'id'   => $default_id,
				'zoom' => $default_zoom,
				'title' => __('Our Location', GUESTABA_CMP_TEXTDOMAIN)
			),
			$atts );

		extract( $atts_actual );

		ob_start();
		?>
		<div class="location-container">
			<h2 class="location-map-title"><?php  echo $title ;?></h2>
			<div class="location-address">
				<p class="location-address-text">
					<?php  echo $location_address; ?>
				</p>
			</div>
			<div class="location-map" id="location-map-<?php echo $id ; ?>" data-zoom="<?php echo $zoom; ?>"</div>

		</div>
		<?php
		$output = ob_get_clean();

		return $output;
	}

/* Location map shortcode. Displays map of facility location.
*
* @since 1.0.4
* @param array $atts
* @return string shortcode output.
*/
	public function corvomap( $atts ) {

		$default_id = get_the_ID();

		/** @var  $id */
		/** @var $zoom */
		/** @var $title */
		/** @var  $activity_title  */
		$atts_actual = shortcode_atts(
			array(
				'id'   => $default_id,
				'zoom' => '14',
				'activity_title' => __('Map Activity', GUESTABA_CMP_TEXTDOMAIN)
			),
			$atts );



		extract( $atts_actual );

		$lat = get_post_meta( $id, 'meta_map_center_lat', true);
		$lng = get_post_meta( $id, 'meta_map_center_lng', true);
		$sw_lat = get_post_meta( $id, 'meta_map_sw_bnds_lat', true);
		$sw_lng = get_post_meta( $id, 'meta_map_sw_bnds_lng', true);
		$ne_lat = get_post_meta( $id, 'meta_map_ne_bnds_lat', true);
		$ne_lng = get_post_meta( $id, 'meta_map_ne_bnds_lng', true);

		$zoom = get_post_meta( $id, 'meta_map_zoom', true);
		$proposal_types = get_post_meta($id, 'meta_proposal_type_list', true);
		$title = get_the_title( $id );

			ob_start();
		?>
		<div class="crowdmap-shortcode">
			<div class="crowd-map-post-content">
				<div class="cmp-map-icon"><i class="fa fa-globe"></i></div>
				<div class="cmp-mobile-title"><h2><?php  echo $title ;?></h2></div>
				<?php echo get_the_content( $id ); ?>
			</div>
			<div class="crowd-map-container">
				<div class="crowd-map-header">
					<div class="cmp-info-icon"><i class="fa fa-info"></i></div>
					<h2 class="crowd-map-title"><?php  echo $title ;?></h2>
					<div class="crowd-map-control-panel">
						<div class="crowd-map-controls">
							<label class="crowd-map-mode-label" for="crowd-map-mode"><?php _e('Mode:', GUESTABA_CMP_TEXTDOMAIN) ?></label>
							<input class="crowd-map-mode-radio" type="radio" name="crowd-map-mode" value="normal" checked/><?php _e('Normal', GUESTABA_CMP_TEXTDOMAIN) ?>
							<input class="crowd-map-mode-radio" type="radio" name="crowd-map-mode" value="heatmap"/><?php _e('Heatmap', GUESTABA_CMP_TEXTDOMAIN) ?>
							<input class="crowd-map-mode-radio" type="radio" name="crowd-map-mode" value="cluster"/><?php _e('Cluster', GUESTABA_CMP_TEXTDOMAIN) ?>
						</div>
						<div class="crowd-map-layer-controls">
							<label class="crowd-map-layers-label"><?php _e('Layers:', GUESTABA_CMP_TEXTDOMAIN) ?></label>
							<input class="crowd-map-layers-checkbox" type="checkbox" name="crowd-map-layers-transit" value="transit"/><?php _e('Transit', GUESTABA_CMP_TEXTDOMAIN)?>
							<input class="crowd-map-layers-checkbox" type="checkbox" name="crowd-map-layers-bike" value="bike"/><?php _e('Bike', GUESTABA_CMP_TEXTDOMAIN)?>
							<input class="crowd-map-layers-checkbox" type="checkbox" name="crowd-map-layers-traffic" value="traffic"/><?php _e('Traffic', GUESTABA_CMP_TEXTDOMAIN)?>
						</div>
					</div>
				</div>

				<div class="crowd-map-form-container" id="crowd-map-form-container-<?php echo $id ; ?>">
					<div class="proposal-form-dismiss" id="proposal-cancel"><i class="fa fa-close"></i></div>
					<div class="checkbox crowd-map-positioning">
						<label><input class="crowd-map-position-checkbox" type="checkbox" name="crowd-map-position-use-current" value="true"/><?php _e('Use current location', GUESTABA_CMP_TEXTDOMAIN)?></label>
					</div>
					<div id="cmp-status-message-container"></div>
					<form role="form" title="Put your idea on the map.">
						<div class="form-group">
							<input class="proposal-title" name="proposal-title" class="map-proposal-title" type="text" placeholder="Enter a title here." value="" required>
						</div>
						<div class="form-group">
							<textarea class="proposal-description" name="proposal-description" class="map-proposal-description" placeholder="Enter a description here." required></textarea>
						</div>
						<div class="form-group">
							<label class="proposal-type-label" for="proposal-type"><?php _e('Select which type of project.', GUESTABA_CMP_TEXTDOMAIN) ?></label>
							<select name="proposal-type" class="form-control proposal-type" required>
							<option value="" selected></option>
							<?php
							foreach ( $proposal_types as $proposal_type ) {
								$title = $proposal_type['title'];
								$icon = $proposal_type['icon'];
								?>
								<option value="<?php echo $title ?>"><?php echo $title ?></option>
							<?php
							}
							?>
							</select>
						</div>
						<div class="proposal-submit-button form-group">
							<input type="button" id="proposal-submit" class="btn btn-default proposal-form-button" vaLue="Submit">
						</div>
					</form>
					<div class="proposal-form-status"></div>
				</div>
				<div class="map-with-activity">
					<div class="crowd-map" id="crowd-map-<?php echo $id ; ?>" data-zoom="<?php echo $zoom; ?>" data-center-lat="<?php echo $lat; ?>" data-center-lng="<?php echo $lng; ?>" data-sw-lat="<?php echo $sw_lat; ?>" data-sw-lng="<?php echo $sw_lng; ?>" data-ne-lat="<?php echo $ne_lat; ?>" data-ne-lng="<?php echo $ne_lng; ?>"></div>
					<div class="map-activity-shortcode">
						<div class="map-activity-title-container">
							<div class="map-activity-mobile-icon"><h4 class="map-activity-mobile-title"><i class="fa fa-comments-o"></i></h4></div>
							<div class="map-activity-toggle"></div>
						</div>
						<div class="map-activity-listing" id="map-activity-listing-<?php echo $id ; ?>"</div>
					</div>
				</div>
			</div>
			<div class="cmp-to-top-link"><a href="#"><i class="fa fa-arrow-circle-up"></i></a></div>
		</div>
		<?php

		$output = ob_get_clean();


		return $output;
	}


	public function map_activity( $atts ) {

		$default_id = get_the_ID();

		/** @var  $id */
		/** @var $title */
		$atts_actual = shortcode_atts(
			array(
				'id'   => $default_id,
				'title' => __('Map Activity', GUESTABA_CMP_TEXTDOMAIN)
			),
			$atts );

		extract( $atts_actual );

		$output =
		'<div class="map-activity-shortcode">
			<div class="map-activity-title-container">
				<h3 class="map-activity-title">' . $title . '</h3>
			</div>
			<div class="map-activity-listing" id="map-activity-listing-' .  $id  . '">
			</div>
		</div>';

		return $output;

	}


}

?>