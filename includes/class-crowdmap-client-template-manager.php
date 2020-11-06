<?php
/**
 *
 */

class CrowdMap_Client_Template_Manager {

	public static function get_info_window_template() {
		return '<div class="cmp-msg-div"><p class="cmp-msg-content"></p></div>
				<div class="cmp-proposal-display">
					<ul class="nav nav-tabs">
					  <li class="active" data-toggle="tab"><a href="#proposal-info-listing"><i class="fa fa-info"></i></a></li>
					  <li data-toggle="tab"><a href="#proposal-comment-listing"><i class="fa fa-comments-o"></i></a></li>
					  <li data-toggle="tab"><a href="#proposal-comment-posting"><i class="fa fa-edit"></i></a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane fade in active" id="proposal-info-listing">
							<h6 id="proposal-title">__proposal.title__</h6>
							<span class="proposal-type-icon map-icon map-icon-__proposal.type.icon__"></span><p id="proposal-type">__proposal.type__, <span class="proposal-author">__proposal.author__</span></p>
							<div id="support-container" class="support-container">
								<button id="support-button-__proposal.id__" class="support-button" title="' . __('Click to show your support of this proposal.', GUESTABA_CMP_TEXTDOMAIN)  . '" data-id="__proposal.id__">' . __('Support', GUESTABA_CMP_TEXTDOMAIN ) . ' </button>
								<div id="support-count-__proposal.id__" class="support-count"><i class="fa fa-heart"></i><span id="badge-__proposal.id__" class="badge">__support.count__</span></div>
							</div>
							<div id="proposal-description "class="proposal-description">
								__proposal.description__
								<br><a class="proposal-page-link" href="__proposal.pageURL__" target="_blank">' . __('See proposal page', GUESTABA_CMP_TEXTDOMAIN) . '</a>
							</div>
					    </div>
					    <div class="tab-pane fade" id="proposal-comment-listing">
							<div id="proposal-comments" class="proposal-comments">
								<h4>' .  __('Comments', GUESTABA_CMP_TEXTDOMAIN) . '</h4>
								__proposal.comments__
							</div>
					    </div>
					    <div class="tab-pane fade" id="proposal-comment-posting">
					        <form id="proposal-comments-form" class="proposal-comment-form">
								<textarea rows="5" class="proposal-comment-input" placeholder="Enter a comment here."></textarea>
								<input type="button" class="proposal-comment-button" value="Comment" data-id="__proposal.id__">
							</form>
							<div class="proposal-edit-link"><a href="__proposal.editURL__" target="_blank">' . __('Edit proposal', GUESTABA_CMP_TEXTDOMAIN ) .  '</a></div>
					    </div>
				    </div>
			    </div>';

	}

	public static function get_activity_item_template() {

		return '<li>
					<div class="map-activity-item-container" data-lat="__mapActivity.lat__" data-lng="__mapActivity.lng__">
						<div class="map-activity-avatar" title="__mapActivity.author__">__mapActivity.avatar__</div>
						<div class="map-activity-author"></div>
						<div class="map-activity-type">__mapActivity.activity_type__ </div>
						<div class="map-activity-description">__mapActivity.description__</div>
						<div class="map-activity-date"> __mapActivity.date__ </div>
					</div>
				</li>';
	}

	public static function get_activity_list_template() {
		return '<ul id="map-activity-list" class="map-activity-list"> __mapActivity.item__ </ul>';
	}

	public static function get_proposal_table_template() {
		return '<table class="wp-list-table widefat fixed posts proposal-table">__proposal.tableRows__</table>';
	}

	public static function get_proposal_table_row_template() {
		return '<tr class="proposal-table-row"><td><a href="__proposal.permalink__" target="_blank">__proposal.id__</a></td><td>__proposal.title__</td><td>__proposal.proposal_type__</td><td>__proposal.supportCount__</td><td>__proposal.lat__</td><td>__proposal.lng__</td></tr>';
	}

	public static function get_proposal_table_heading_template() {
		return '<thead><tr class="proposal-table-heading"><th>' . __('ID', GUESTABA_CMP_TEXTDOMAIN ) . '</th><th>' . __('Title', GUESTABA_CMP_TEXTDOMAIN ) . '</th><th>' . __('Type', GUESTABA_CMP_TEXTDOMAIN ) . '</th><th>' . __('Support Count', GUESTABA_CMP_TEXTDOMAIN ) . '</th><th>' . __('Latitude', GUESTABA_CMP_TEXTDOMAIN ) . '</th><th>' . __('Longitude', GUESTABA_CMP_TEXTDOMAIN ) . '</th></tr></thead>';
	}

	public static function get_proposal_form_status_template() {
		return '<div class"proposal-form-status"><p>__proposalForm.status__ <i class="cmp-proposal-status-close fa fa-close"></i></p></div>';
	}
}