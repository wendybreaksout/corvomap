

var CMP_POS_DECIMAL = 14;
var CMP_ACTIVITY_TOGGLE_ON_HTML = '<i class="fa fa-caret-up"></i>';
var CMP_ACTIVITY_TOGGLE_OFF_HTML = '<i class="fa fa-caret-down"></i>';

var crowdMap;
var lastActivityRefresh = Date.now();
var lastProposalRefresh = Date.now();
var activities = [];
var proposals = [];
var retrievedActivities = false;
var retrievedProposals = false;
var markerList = [];
var openInfoWindow;
var infoWindow  ;
var heatmap;
var heatmapOn = false;
var markerClusterer;
var markerClustererOn = false;
var showActivityOn = true;
var mapMode = 'normal';
// var mapContainer = document.getElementById('crowd-map-' + objectl10n.postID ) ;
var mapContainer;
var transitLayer = new google.maps.TransitLayer();
var bikeLayer = new google.maps.BicyclingLayer();
var trafficLayer = new google.maps.TrafficLayer();


var spinnerOptions = {
    lines: 9 // The number of lines to draw
    , length: 5 // The length of each line
    , width: 2 // The line thickness
    , radius: 4 // The radius of the inner circle
    , scale: 1 // Scales overall size of the spinner
    , corners: 1 // Corner roundness (0..1)
    , color: '#000' // #rgb or #rrggbb or array of colors
    , opacity: 0.6 // Opacity of the lines
    , rotate: 0 // The rotation offset
    , direction: 1 // 1: clockwise, -1: counterclockwise
    , speed: 1 // Rounds per second
    , trail: 60 // Afterglow percentage
    , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
    , zIndex: 2e9 // The z-index (defaults to 2000000000)
    , className: 'spinner' // The CSS class to assign to the spinner
    , top: '50%' // Top position relative to parent
    , left: '50%' // Left position relative to parent
    , shadow: false // Whether to render a shadow
    , hwaccel: false // Whether to use hardware acceleration
    , position: 'absolute' // Element positioning
}

var spinner = new Spinner( spinnerOptions );

// Desktop/tablet map options
var crowdMapOptions = {
    mapTypeControlOptions: {
        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
        position: google.maps.ControlPosition.BOTTOM_LEFT
    },
    zoomControlOptions: {
        position: google.maps.ControlPosition.LEFT_BOTTOM
    },
    streetViewControlOptions: {
        position: google.maps.ControlPosition.LEFT_BOTTOM
    }
};

jQuery(document).ready( function (){
    formatForMobile();
    initCrowdMap();
});


function initCrowdMap() {

    mapContainer = document.getElementById('crowd-map-' + objectl10n.postID ) ;

    var zoomSetting = jQuery( mapContainer ).data('zoom');
	var centerLat =  parseFloat( jQuery( mapContainer ).data('center-lat') );
	var centerLng =  parseFloat( jQuery( mapContainer ).data('center-lng') );



	// parameters for bounds
	var swLat = parseFloat( jQuery( mapContainer ).data('sw-lat') );
	var swLng = parseFloat( jQuery( mapContainer ).data('sw-lng') );
	var neLat = parseFloat( jQuery( mapContainer ).data('ne-lat') );
	var neLng = parseFloat( jQuery( mapContainer ).data('ne-lng') );
    var mapDim = { height: jQuery( mapContainer).height(), width: jQuery( mapContainer ).width()  };

	var centerLatLng = {lat: centerLat, lng: centerLng };


	crowdMap = new google.maps.Map( mapContainer , {
		center: centerLatLng,
		zoom: zoomSetting,
        scrollwheel: false,
        mapTypeControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles: [
            {stylers: [{ visibility: 'simplified' }]},
            {elementType: 'labels', stylers: [{ visibility: 'on' }]}
        ]
	});

    crowdMap.setOptions( crowdMapOptions) ;

    crowdMap.addListener('idle', function() {
        if ( heatmapOn ) {
            // hide marker icons
            jQuery('.map-icon').css('display','none');

        }
    });

    infoWindow = new google.maps.InfoWindow();

    jQuery('input[type=radio][name=crowd-map-mode]').change(function() {

        switch ( this.value ) {
            case 'normal':
                mapMode = 'normal';
                showMarkers();
                hideHeatmap();
                hideClusterer();
                break;

            case 'heatmap':
                mapMode = 'heatmap';
                hideClusterer();
                hideMarkers();
                showHeatmap();

                var interval = setInterval( function() {
                    jQuery('.map-icon').css('display','none');
                    clearInterval( interval );
                }, 500);

                break;

            case 'cluster':
                mapMode = 'cluster';
                showClusterer();
                showMarkers();
                hideHeatmap();
                break;

            default:
                // This would be the cluster ____ case
                console.log("Error: unexpected value in crowd-map-mode radio button.");
        }


    });

    jQuery('input[type=checkbox][name=crowd-map-layers-transit]').change(function() {
        if ( this.checked ) {
            transitLayer.setMap( crowdMap );
        }
        else {
            transitLayer.setMap( null );
        }
    });

    jQuery('input[type=checkbox][name=crowd-map-layers-bike]').change(function() {
        if ( this.checked ) {
            bikeLayer.setMap( crowdMap );
        }
        else {
            bikeLayer.setMap( null );
        }
    });

    jQuery('input[type=checkbox][name=crowd-map-layers-traffic]').change(function() {
        if ( this.checked ) {
            trafficLayer.setMap( crowdMap );
        }
        else {
            trafficLayer.setMap( null );
        }
    });



	crowdMap.addListener('click', function(e) {
        if ( mapMode == "normal" ) {
            if ( objectl10n.is_user_logged_in ) {
                placeMarkerAndPanTo(e.latLng, crowdMap);
            }

        }

	});

    // event handler for heatmap button
    jQuery('.crowd-map-heatmap-button').click( function() {
        if ( heatmapOn ) {
            hideHeatmap();
        }
        else {
            showHeatmap();
        }
    });

    // event handler for clusterer button
    jQuery('.crowd-map-clusterer-button').click( function() {
        if ( markerClustererOn ) {
            hideClusterer();
        }
        else {
            showClusterer();
        }
    });

    /* Actvitivy show/hide */
    showActivity();
    jQuery('.map-activity-toggle').click( function() {
        if ( showActivityOn ) {
            hideActivity();
        }
        else {
            showActivity();
        }
    });


    // bounds limits if configured.
    if ( ( neLat != 0 && swLat != 0 ) || neLat == NaN || swLat == NaN || neLng == NaN || swLat == NaN ) {
        var strictBounds = new google.maps.LatLngBounds(
            new google.maps.LatLng( swLat, swLng ),
            new google.maps.LatLng( neLat, neLng ));

        var minZoom = getBoundsZoomLevel( strictBounds, mapDim );

        // Listen for the dragend event
        crowdMap.addListener( 'dragend', function () {

            if (strictBounds.contains( crowdMap.getCenter())) return;

            // We're out of bounds - Move the map back within the bounds
            var c = crowdMap.getCenter(),
                x = c.lng(),
                y = c.lat(),
                maxX = strictBounds.getNorthEast().lng(),
                maxY = strictBounds.getNorthEast().lat(),
                minX = strictBounds.getSouthWest().lng(),
                minY = strictBounds.getSouthWest().lat();

            if (x < minX) x = minX;
            if (x > maxX) x = maxX;
            if (y < minY) y = minY;
            if (y > maxY) y = maxY;

            crowdMap.setCenter(new google.maps.LatLng(y, x));
        });

        crowdMap.addListener( 'zoom_changed', function () {
            if (crowdMap.getZoom() < minZoom ) crowdMap.setZoom( minZoom );
        });

    }



	displayProposals( crowdMap );
	displayActivity();
	refreshActivity();

    // This is a workaround for a problem in which the icons don't line up with the markers
    // when the map is first initilized. Changing the zoom level causes the markers to line up.
    var correctIconsInt = setInterval( function( ){
        var currentZoom = crowdMap.getZoom();
        crowdMap.setZoom( currentZoom + 1 );
        crowdMap.setZoom( currentZoom );
        clearInterval( correctIconsInt );
    }, 3000);




}

function placeMarkerAndPanTo(latLng, map) {
	var marker = new google.maps.Marker({
		position: latLng,
		map: map,
        zIndex: 1000000
	});
	map.panTo(latLng);
	jQuery('.crowd-map-form-container').css('display', 'block');


	jQuery('#proposal-cancel').click(function () {
		jQuery('.crowd-map-form-container').css('display', 'none');
		marker.setMap(null);
        jQuery('.crowd-map-position-checkbox').prop('checked', false );

	});

    jQuery('input[type=checkbox][name=crowd-map-position-use-current]').change(function() {
        if ( this.checked ) {
            useCurrentLocation( marker, map );
        }
        else {

        }
    });




	jQuery('#proposal-submit').unbind().click(function () {


		var proposal = {};
		// data to retrieve from marker:
		proposal.lat = marker.position.lat();
		proposal.lng = marker.position.lng();

		// To retrieve from form.
		proposal.title = jQuery('.proposal-title').val();
		proposal.description = jQuery('.proposal-description').val();
		proposal.proposal_type = jQuery('.proposal-type').val();


        if ( ( proposal.title == '' ) || ( proposal.description == '' ) || ( proposal.proposal_type == '') ) {
            displayStatusMessage( objectl10n.all_fields_required );
        }
        else {
            jQuery('.proposal-title').val('');
            jQuery('.proposal-description').val('');
            jQuery('.proposal-type').val('');
            jQuery('.crowd-map-form-container').css('display', 'none');
            postProposal(proposal, map, marker);
        }

	});
}

function postProposal( proposal, map, marker ) {

    showSpinner();

	jQuery.ajax({
		url:  objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
		data:{
			'action':'crowdmap_ajax',
			'fn':'publish_proposal',
			'documentURL' : document.URL,
			'postID' : objectl10n.postID,
			'proposal' : proposal
		},
		dataType: 'JSON',
		success:function(data){

            stopSpinner();

			if ( data.errorData != null && data.errorData == 'true' ) {
				reportError( data );
				return;
			}

			if ( data != false ) {

				// Remove orginal marker. A new one will be created.
				marker.setMap(null);

				// TODO: change icon

				var proposal = data;
				displayProposal( proposal, map );
				displayActivity();

			}
			else {
				// TODO: localize this message.
				console.log('AJAX call to post_proposal returned false');
			}
		},
		error: function(errorThrown){
            stopSpinner();
			console.log( objectl10n.publish_proposal_error + errorThrown.responseText.substring(0,500) );
		}

	});
}

function displayProposals( map ) {

    /*
     * Setting since = 0 only all retrievals will download all of the proposals for the map.
     * Sending lastProposalRefresh will get only those from the last refresh. That
     * needs be combined with the ability to also get updated proposals. For now,
     * all proposals are refreshed (replaced) in order to update the local copies.
     */
	var since = 0;

    if ( retrievedProposals === false ) {
        showSpinner();
    }


	jQuery.ajax({
		url:  objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
		data:{
			'action':'crowdmap_ajax',
			'fn':'get_proposals',
			'documentURL' : document.URL,
			'mapID' : objectl10n.postID,
			'since' : since
		},
		dataType: 'JSON',
		success:function(data){

            stopSpinner();

			if ( data.errorData != null && data.errorData == 'true' ) {
				reportError( data );
				return;
			}
			// data is expected to be an array of proposals or false.
			if ( data !== false ) {

				retrievedProposals = true;
				// reset time of update
				lastProposalRefresh = Date.now();
				proposals = data ;

				for ( var i = 0 ; i < proposals.length ; i++ ) {
					var proposal = proposals[i];
					displayProposal( proposal, map );
				}


			}
			else {
				// remove marker.
				marker.setMap(null);
				// TODO: localize this message.
				console.log('AJAX call to post_proposal returned false');
			}
		},
		error: function(errorThrown){
            stopSpinner();
			console.log( objectl10n.get_multiple_proposals_error + errorThrown.responseText.substring(0,500) );
		}

	});


}



function displayProposal( proposal, map ) {

	var infoContent = objectl10n.proposal_info_template;
	infoContent = infoContent.replace('__proposal.title__', proposal.title );
    infoContent = infoContent.replace('__proposal.author__', proposal.author );
	infoContent = infoContent.replace( '__proposal.description__', proposal.description );
    infoContent = infoContent.replace( '__support.count__', proposal.support_count );
	infoContent = infoContent.replace( /__proposal.id__/g, proposal.id );

    var editURL = objectl10n.wpsiteinfo.site_url + '/wp-admin/post.php?post=' + proposal.id + '&action=edit' ;
    infoContent = infoContent.replace( /__proposal.editURL__/g, editURL );

    infoContent = infoContent.replace( /__proposal.pageURL__/g, proposal.permalink );

	var commentContent = formatComments( proposal.comments );

	infoContent = infoContent.replace( '__proposal.comments__', commentContent );

    propLat = parseFloat( proposal.lat );
    propLng = parseFloat( proposal.lng );

	var latLng = {
		lat: propLat,
		lng: propLng
	};

    var icon ;
    var proposal_type;

    if ( proposal.proposal_type != undefined && proposal.proposal_type != "" && proposal.proposal_type.length > 0 ) {
        var proposal_type = objectl10n.proposal_types.filter(function (item) {
            return item.title === proposal.proposal_type;
        });
        if ( proposal_type[0].icon != undefined) {
            icon = proposal_type[0].icon;
        }
        else {
            icon = objectl10n.default_map_icon;
        }
    }
    else {
        icon = objectl10n.default_map_icon;
    }

    var tooltip = proposal.proposal_type + ', ' + proposal.title;


    // Try to get marker from marker list first, to see if there's already one there.
    var marker = getMarkerAtPosition( propLat, propLng );
    if ( ! marker ) {
        // marker = new google.maps.Marker({
        marker = new Marker({
            position: latLng,
            map: map,
            title: tooltip,
            visible: !heatmapOn,
            icon:  {
                path: SQUARE_PIN,
                fillColor: proposal_type[0].marker_color,
                fillOpacity: 1,
                strokeColor: '',
                strokeWeight: 0
            },
            map_icon_label: '<span style="color:'+ proposal_type[0].icon_color + ';" class="map-icon map-icon-' + icon + '"></span>'
        });
    }



    // add icon to infoContent
    infoContent = infoContent.replace( '__proposal.type.icon__', icon );
    infoContent = infoContent.replace( '__proposal.type__', proposal.proposal_type );

	marker.infoContent = infoContent ;
	marker.proposal = proposal ;



	marker.addListener('click', function() {
        /*
		var infoWindow = new google.maps.InfoWindow({
			position: this.getPosition(),
			content: this.infoContent,
		});
        */

        hideActivity();
        jQuery('.map-activity-shortcode').css('z-index', '-2');

        infoWindow.setPosition( this.getPosition() ) ;
        infoWindow.setContent(  this.infoContent );

        google.maps.event.addListener( infoWindow, 'closeclick', function() {
            var infoPosition = this.position;
            // this.close();
            map.setCenter( infoPosition );
            showActivity();
        });

		infoWindow.open( map, this );
        openInfoWindow = infoWindow;


        jQuery('.nav-tabs a').click(function(){
            jQuery(this).tab('show');
        });


        if ( objectl10n.is_user_logged_in ) {
            // event handler for comment posting is here.
            infoWindow.addListener('domready', function () {



                jQuery('.proposal-comment-button').unbind().click(function (e) {
                    var id = jQuery('.proposal-comment-button').data('id');
                    var comment = jQuery('.proposal-comment-input').val();
                    var proposal = marker.proposal;
                    map.setCenter(marker.getPosition());
                    infoWindow.close();
                    postComment(id, comment);
                    refreshProposal(id, map);
                    showActivity();
                });


                if (proposal.user_has_supported == true) {
                    jQuery('#support-button-' + proposal.id).attr('disabled', true);

                    jQuery('#support-button-' + proposal.id).text( objectl10n.supported_label );

                }


                jQuery('.support-button').unbind().click(function (e) {
                    var id = jQuery(this).data('id');
                    var proposal = marker.proposal;

                    // counter element is passed to postSupport so that it can be updated.
                    var counterElement = jQuery('#badge-' + id);
                    postSupport(id, counterElement);

                    refreshProposal(id, map);
                    this.blur();
                });
            });
        }
        else {
            jQuery('.proposal-comment-button').remove();
            jQuery('.proposal-comment-input').remove();
            jQuery('.proposal-edit-link').remove();
            jQuery('#support-button-' + proposal.id).remove();
            jQuery('.cmp-msg-content').html( objectl10n.not_logged_in_msg );
        }

	});


    appendToMarkerList( marker );

}


function displayActivity() {

	var since ;

	if ( retrievedActivities ) {
		since = lastActivityRefresh;
	}
	else {
		since = 0;
	}

	// TODO: need to pass mapID as param. Using postID not enable the map shortcode to work on any page.
	jQuery.ajax({
		url:  objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
		data:{
			'action':'crowdmap_ajax',
			'fn':'get_map_activity',
			'documentURL' : document.URL,
			'mapID' : objectl10n.postID,
			'since' : since
		},
		dataType: 'JSON',
		success:function(data){
			if ( data.errorData != null && data.errorData == 'true' ) {
				reportError( data );
				return;
			}
			// data is expected to be 'true' or 'false'
			if ( data !== false ) {

				retrievedActivities = true;
				// reset time of update
				lastActivityRefresh = Date.now();

				var retrieved_activities = data;

				activities = retrieved_activities.concat( activities ) ;

				var formattedItems = '';
				for ( var i = 0 ; i < activities.length; i++ ) {

					var formattedItem = objectl10n.activity_item_template;
                    formattedItem = formattedItem.replace('__mapActivity.avatar__', activities[i].avatar );
					formattedItem = formattedItem.replace('__mapActivity.date__', getIntervalString( activities[i].date  ) );
					formattedItem = formattedItem.replace('__mapActivity.author__', activities[i].author );
					formattedItem = formattedItem.replace('__mapActivity.activity_type__', activities[i].activity_type );
					formattedItem = formattedItem.replace('__mapActivity.description__', activities[i].description );
                    formattedItem = formattedItem.replace('__mapActivity.lat__', activities[i].lat );
                    formattedItem = formattedItem.replace('__mapActivity.lng__', activities[i].lng );
                    formattedItems += formattedItem;
				}

				var formattedList = objectl10n.activity_list_template;
				formattedList = formattedList.replace('__mapActivity.item__', formattedItems );
				var idSelector = "#map-activity-listing-" + objectl10n.postID ;
				jQuery( idSelector).html( formattedList );
                jQuery('.map-activity-item-container').click(function(){
                    var lat = jQuery(this).data('lat');
                    var lng = jQuery(this).data('lng');
                    crowdMap.setCenter ({ 'lat': lat, 'lng': lng} );
                    for ( var i = 0 ; i  < markerList.length; i++  ){
                        var position = markerList[i].getPosition();
                        var markerLat = position.lat();
                        var markerLng = position.lng();

                        // Normalize number of decimal places
                        lat = parseFloat( lat.toFixed(7));
                        lng = parseFloat( lng.toFixed(7));
                        markerLat = parseFloat( markerLat.toFixed(7));
                        markerLng = parseFloat( markerLng.toFixed(7));

                        if ( markerLat == lat && markerLng == lng ) {
                            if ( openInfoWindow != undefined ) {
                                openInfoWindow.close();
                            }
                            google.maps.event.trigger( markerList[i], 'click');
                        }
                    }

                });
			}
			else {
				// TODO: localize this message.
				console.log('AJAX call to get_map_activity returned false');
			}
		},
		error: function(errorThrown){
			console.log( objectl10n.get_activity_error + errorThrown.responseText.substring(0,500) );
		}

	});

}

function refreshActivity() {
	setInterval( function() {
		displayActivity();
		displayProposals( crowdMap );
        refreshHeatmap();
	}, objectl10n.activity_refresh_interval );

}

function postComment( proposalID, content ) {
	jQuery.ajax({
		url:  objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
		data:{
			'action':'crowdmap_ajax',
			'fn':'post_comment',
			'documentURL' : document.URL,
			'proposalID' : proposalID,
			'content' : content
		},
		dataType: 'JSON',
		success:function(data){
			if ( data.errorData != null && data.errorData == 'true' ) {
				reportError( data );
				return;
			}
			// data is expected to be 'true' or 'false'
			if (  data !== false ) {
				displayActivity();
			}
			else {
				// TODO: localize this message.
				console.log('AJAX call to post_comment returned false');
			}
		},
		error: function(errorThrown){
			console.log(objectl10n.post_comment_error + errorThrown.responseText.substring(0,500));
		}

	});

}

function postSupport( proposalID, counterElement ) {
	jQuery.ajax({
		url:  objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
		data:{
			'action':'crowdmap_ajax',
			'fn':'post_support',
			'documentURL' : document.URL,
			'proposalID' : proposalID
		},
		dataType: 'JSON',
		success:function(data){
			if ( data.errorData != null && data.errorData == 'true' ) {
				reportError( data );
				return;
			}
			// data is expected to be 'true' or 'false'
			if (  data === true ) {
                // increment support count.
                var count =  parseInt( jQuery( counterElement).text() ) + 1;
                jQuery( counterElement).text( count );
                jQuery('#support-button-' + proposalID ).attr('disabled', true);
                jQuery('#support-button-' + proposalID ).text('Supported');
				displayActivity();
			}
            else if ( data == -1 ) {
                // already voted.
            }
			else {
				// TODO: localize this message.
				console.log('AJAX call to post_comment returned false');
			}
		},
		error: function(errorThrown){
			console.log( objectl10n.post_support_error + errorThrown.responseText.substring(0,500) );
		}

	});

}

function formatComments( comments) {

	if ( comments == undefined || comments.length == 0) {
		return "";
	}

	var formattedComments = '<ul class="proposal-comments-list">';

	for( var i = 0; i < comments.length ; i++ ) {
        // comments with content of '1' are supports. Filter them out.
        if ( comments[i].comment_content === '1') {
            continue;
        }
		formattedComments += '<li>' + comments[i].comment_content +'</li>'
	}

	formattedComments += '</ul>';

	return formattedComments;
}

function refreshProposal( $proposalID, map ) {

	jQuery.ajax({
		url:  objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
		data:{
			'action':'crowdmap_ajax',
			'fn':'get_proposal',
			'documentURL' : document.URL,
			'proposalID' : $proposalID
		},
		dataType: 'JSON',
		success:function(data){
			if ( data.errorData != null && data.errorData == 'true' ) {
				reportError( data );
				return;
			}
			// data is expected to be 'true' or 'false'
			if ( data != false ) {

				var proposal = data;
				displayProposal( proposal, map );

			}
			else {

				// TODO: localize this message.
				console.log('AJAX call to get_proposal returned false');
			}
		},
		error: function(errorThrown){
			console.log( objectl10n.get_proposal_error + errorThrown.responseText.substring(0,500) );
		}

	});


}


function getIntervalString( timestamp ) {

    var date = new Date();
    var timeNow = date.getTime() / 1000 ; // convert to seconds


    var timeDiff = ( timeNow - timestamp ) ;

    var sinceStrBefore = '';
    if ( objectl10n.since_string_before !== 'off') {
        sinceStrBefore = objectl10n.since_string_before + ' ';
    }

    var intervalString ;
    // less than an hour, report minutes
    if ( timeDiff < 3600 ) {
        var minutes = Math.ceil( timeDiff / 60 );
        intervalString = sinceStrBefore +  minutes.toString() + ' ' + objectl10n.minutes + ' ' + objectl10n.since_string_after ;
    }
    else if ( timeDiff >= 3600 && timeDiff < 86400 ) {
        var hours = Math.ceil( timeDiff / 3600 ) ;
        intervalString = sinceStrBefore + hours.toString() + ' ' + objectl10n.hours + ' ' + objectl10n.since_string_after ;
    }
    else if ( timeDiff >= 86400 && timeDiff < 25920000 ) {
        var days = Math.ceil( timeDiff / 86400 ) ;
        intervalString = sinceStrBefore + days.toString() + ' ' + objectl10n.days + ' ' + objectl10n.since_string_after ;
    }
    else {
        intervalString = objectl10n.over_a_year;
    }

    return intervalString;
}


function appendToMarkerList ( marker ) {

    // See if marker is already on this list. If so, don't add it.
    var markerPos = marker.getPosition();
    var markerLat = markerPos.lat();
    var markerLng = markerPos.lng();

    if ( markerExists( markerLat, markerLng )) {
        return;
    }
    // Ok to add to list.
    markerList.push( marker );
}

/*
 * markerExists -- determine if marker exists at postion. Return true if yes, false if no.
 */

function markerExists( lat, lng ) {

    if ( ! getMarkerAtPosition( lat, lng ) ) {
        return false;
    }
    else {
        return true;
    }
}

function reportError( errorData ) {
	var errorString = objectl10n.server_error + errorData.errorMessage ;
	alert( errorString );
}

/*
 *
 * Code borrowed from this Stack Overflow discussion:
 * http://stackoverflow.com/questions/6048975/google-maps-v3-how-to-calculate-the-zoom-level-for-a-given-bounds
 */
function getBoundsZoomLevel(bounds, mapDim) {
    var WORLD_DIM = { height: 256, width: 256 };
    var ZOOM_MAX = 21;

    function latRad(lat) {
        var sin = Math.sin(lat * Math.PI / 180);
        var radX2 = Math.log((1 + sin) / (1 - sin)) / 2;
        return Math.max(Math.min(radX2, Math.PI), -Math.PI) / 2;
    }

    function zoom(mapPx, worldPx, fraction) {
        return Math.floor(Math.log(mapPx / worldPx / fraction) / Math.LN2);
    }

    var ne = bounds.getNorthEast();
    var sw = bounds.getSouthWest();

    var latFraction = (latRad(ne.lat()) - latRad(sw.lat())) / Math.PI;

    var lngDiff = ne.lng() - sw.lng();
    var lngFraction = ((lngDiff < 0) ? (lngDiff + 360) : lngDiff) / 360;

    var latZoom = zoom(mapDim.height, WORLD_DIM.height, latFraction);
    var lngZoom = zoom(mapDim.width, WORLD_DIM.width, lngFraction);

    return Math.min(latZoom, lngZoom, ZOOM_MAX);
}

function showClusterer() {

    if ( markerClusterer == undefined) {
        markerClusterer = new MarkerClusterer( crowdMap, markerList );
    }
    else {
        markerClusterer.setMap( crowdMap );
    }

    markerClustererOn = true;
}

function hideClusterer() {
    if ( markerClusterer != undefined ) {
        markerClusterer.setMap( null );
    }
    markerClustererOn = false;
}

function refreshHeatmap() {

    if ( heatmap == undefined ) {
        heatmap = new google.maps.visualization.HeatmapLayer();
    }

    heatmap.setData( getHeatmapPoints());


}

function showHeatmap() {

    if ( heatmap == undefined ) {
        heatmap = new google.maps.visualization.HeatmapLayer({
            data: getHeatmapPoints()
        });
    }

    heatmap.setMap( crowdMap );
    heatmapOn = true;



}


function hideHeatmap() {

    if ( heatmap != undefined ) {
        heatmap.setMap( null );
    }
    // showMarkers();
    heatmapOn = false;
}

function hideMarkers() {
    for ( var i = 0 ; i < markerList.length ; i++ ) {
        markerList[i].setVisible( false );
    }

    jQuery('.map-icon').css('display','none');

}

function showMarkers() {
    for ( var i = 0 ; i < markerList.length ; i++ ) {
        markerList[i].setVisible( true );
    }
    jQuery('.map-icon').css('display','block');
}

function getMarkerAtPosition( lat, lng ) {

    var positionLat = parseFloat( lat.toFixed( CMP_POS_DECIMAL ));
    var positionLng = parseFloat( lng.toFixed( CMP_POS_DECIMAL ));

    for ( var i = 0 ; i  < markerList.length; i++  ){
        var listedPos = markerList[i].getPosition();
        var listedLat = listedPos.lat();
        var listedLng = listedPos.lng();

        listedLat = parseFloat( listedLat.toFixed( CMP_POS_DECIMAL ));
        listedLng = parseFloat( listedLng.toFixed( CMP_POS_DECIMAL ));

        if ( positionLat == listedLat && positionLng == listedLng ) {
            return markerList[i];
        }
    }

    // No marker found.
    return false;

}


function getHeatmapPoints() {

    var hpPoints = [];
    for ( var i = 0 ; i < proposals.length ; i++ ) {

        proposal = proposals[i];
        hpPoints.push( {location: new google.maps.LatLng( proposal.lat , proposal.lng ), weight: 1 + proposal.support_count } );
    }

    return hpPoints;
}

function showSpinner() {
    spinner.spin( mapContainer );
}

function stopSpinner() {
    spinner.stop();
}

/* TODO: The hardcoded values in the next two functions should be configuration options */
function showActivity() {
    jQuery('.map-activity-toggle').html( CMP_ACTIVITY_TOGGLE_ON_HTML );
    jQuery('.map-activity-listing').show();
    jQuery('.map-activity-shortcode').css({ "height" : "500px", "z-index" : "10", "width" : "12em" , "min-width" : "12em", "top" : "0", "right": "0"});
    showActivityOn = true;
}

function hideActivity() {
    jQuery('.map-activity-toggle').html( CMP_ACTIVITY_TOGGLE_OFF_HTML );
    showActivityOn = false;
    jQuery('.map-activity-listing').hide();
    jQuery('.map-activity-shortcode').css('height', jQuery('.map-activity-title-container').css('height') );
    jQuery('.map-activity-shortcode').css({ "width" : "5.5em", "min-width" : "5.5em", "top" : "8px", "right": "8px" });


}

function useCurrentLocation( marker, map ) {

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition( function( position ) {
            var newPos = {
                'lat': position.coords.latitude,
                'lng': position.coords.longitude
            }

            map.panTo( newPos );
            marker.setPosition( newPos );
            marker.setZIndex( 10000 );
        }, showLocationError );
    }
    else {
        displayStatusMessage( objectl10n.geolocation_not_supported );
    }
}



function showLocationError( error ) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            displayStatusMessage( objectl10n.user_denied_geolocation_request );
            break;
        case error.POSITION_UNAVAILABLE:
            displayStatusMessage( objectl10n.location_is_unavailable );
            break;
        case error.TIMEOUT:
            displayStatusMessage( objectl10n.location_request_timeout );
            break;
        case error.UNKNOWN_ERROR:
            displayStatusMessage( objectl10n.unknown_error_occured );
            break;
    }

}

function displayStatusMessage( message ) {

    var statusHTML = objectl10n.proposal_form_status_template ;
    statusHTML = statusHTML.replace('__proposalForm.status__', message );
    jQuery('#cmp-status-message-container').html( statusHTML );
    // Close proposal form status div.
    jQuery('.cmp-proposal-status-close').click( function() {
        jQuery('#cmp-status-message-container').html('');

    });
}

function formatForMobile() {

    if ( objectl10n.display_mobile_format && jQuery(window).width() <= objectl10n.mobile_breakpoint ) {
        var mapHTML = jQuery('.crowd-map-container').html();
        var mapContentHTML = jQuery('.crowd-map-post-content').html();
        jQuery('#page').html('');
        jQuery('#page').html('<div class="crowd-map-post-content"></div><div class="crowd-map-container">' + mapHTML  + '</div>' );
        jQuery('.crowd-map-post-content').html( mapContentHTML );
        var adminBarHeight = jQuery('#wpadminbar').height();
        if ( adminBarHeight ==  null )
            adminBarHeight = 0;
        jQuery('.crowd-map-container').css({ "height" : window.innerHeight - adminBarHeight ,  "position" : "absolute",  "bottom" : "0", "left" : "0", "right" : "0"});
        jQuery('.crowd-map-post-content').css({ "height" : "100%" ,  "width" : "100%", "padding" : "5px"});

        jQuery('.crowd-map-post-content').hide();
        jQuery('.cmp-info-icon').show();

        jQuery('.cmp-map-icon').show();
        jQuery('.cmp-map-icon').css({ "top" : ( adminBarHeight + 5 ) + "px" });
        jQuery('.cmp-mobile-title').show();

        jQuery('.cmp-to-top-link').css({ "top": ( window.innerHeight - 45 ) + 'px' } );
        jQuery('.cmp-to-top-link').show();

        crowdMapOptions = {
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.TOP_LEFT
            },
            zoomControlOptions: {
                position: google.maps.ControlPosition.LEFT_TOP
            },
            streetViewControlOptions: {
                position: google.maps.ControlPosition.LEFT_TOP
            }
        };



        jQuery('.cmp-info-icon').click( function() {
            jQuery('.crowd-map-container').hide();
            jQuery('.crowd-map-post-content').show();
        });

        jQuery('.cmp-map-icon').click( function() {
            jQuery('.crowd-map-post-content').hide();
            jQuery('.crowd-map-container').show();
        });

    }


}