

jQuery(document).ready( function($) {


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
        , className: 'cmp-progress-spinner' // The CSS class to assign to the spinner
        , top: '50%' // Top position relative to parent
        , left: '50%' // Left position relative to parent
        , shadow: false // Whether to render a shadow
        , hwaccel: false // Whether to use hardware acceleration
        , position: 'absolute' // Element positioning
    }

    var spinner = new Spinner( spinnerOptions );

    function showSpinner() {
        spinner.spin( document.getElementById('cmp-spinner') );
    }

    function stopSpinner() {
        spinner.stop();
    }

	var meta_image_frame;

	$('.gst-sortable').sortable();
	$('.gst-sortable').disableSelection();

	$('.cmp-report-map-select').on('change', function() {
		getProposalsRanked( $(this).val() ); // should be map id
        $('#cmp-report-reset-btn').prop("disabled",false);
        $('#cmp-report-export-btn').prop("disabled",false);
	});


	/* This function makes an ajax call to retrieve js configs for post edit input fields and then sets them */
	setPostEditOptions();


	// call 3rd party apis
	$('.image-picker').imagepicker({
            hide_select: false,
            clicked: function ( pickerOptions ) {
                $( pickerOptions.option ).attr("selected","selected");
            }
        }
    );

    // Color picker for proposal type icon colors
	$('.gst-color-input').wpColorPicker();

    // TODO: can this be removed?
	$('.gst_proposal_input').on('change', function() {
		$( this).attr("value", $(this).val() );
	});

	$('.gst_proposal_type_input').on('change', function() {
		$( this).attr("value", $(this).val() );
	});


	$( '#meta_proposal_list' ).on( "sortupdate", function( event, ui ) {
		reInitProposalList( event, $(this) );
	});



	$('.gst-sort-edit-delete').click( function( e ) {
		$( this ).parent().parent().remove();
	});

	$('#meta_proposal_list .gst-sort-edit-delete').click( function( e) {
		$(this).parent().parent().remove();
		reInitProposalList( e, $(this) );
	});


	$('#post').submit( function() {

	});


	$('#cmp-report-reset-btn').click( function() {
        $('.cmp-report-output').html('') ;
        $('#cmp-report-export-btn').prop("disabled",true);
        $('#cmp-report-reset-btn').prop("disabled",true);
        $('.cmp-report-map-select').val('');

	});


    $('#cmp-report-export-btn').click( function() {
        var mapID = $('.cmp-report-map-select').val();
        document.getElementById('cmp-download-iframe').src = cmp_admin_objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php?action=crowdmap_ajax&fn=get_proposals_export&mapID='+ mapID ;
    });

	$('.gst-sort-edit-image-upload').click(function(e){
		handleImageUpload( e, $(this) );
	});

	$('.gst-sort-edit-image-add').click( function( e ) {


		var cloneHTML = atob( $(this).siblings('.gst_clone_template').val() );
		$( this ).parent().parent().children().filter('.gst-sortable').append( cloneHTML );

		// connect event hanlders to new list item.
		$( this ).parent().parent().children().filter('.gst-sortable').children().last().find('.gst-sort-edit-delete').click( function() {
			$( this ).parent().parent().remove();
		});

		$( this ).parent().parent().children().filter('.gst-sortable').children().last().find('.gst-sort-edit-image-upload').click( function() {
			handleImageUpload( e, $(this) );
		});

		$( this ).parent().parent().children().filter('.gst-sortable').children().last().find('.gst_clear_input_target').val('');
		$( this ).parent().parent().children().filter('.gst-sortable').children().last().find('.gst-sort-edit-image-upload').trigger('click');

	});


	$('.gst-sort-edit-add').click( function() {


		var cloneHTML = atob( $(this).siblings('.gst_clone_template').val() );
		$( this ).parent().parent().children().filter('.gst-sortable').append( cloneHTML );

		$( this ).parent().parent().children().filter('.gst-sortable').children().last().find('input').attr('value','');

		$( this ).parent().parent().children().filter('.gst-sortable').children().last().find('.gst-sort-edit-delete').click( function() {
			$( this ).parent().parent().remove();
		});
	});




	$('.gst-sort-edit-proposal-type-add').click( function( e ) {


		var cloneHTML = atob( $(this).siblings('.gst_clone_template').val() );
		$( this ).parent().parent().children().filter('.gst-sortable').append( cloneHTML );

		// erase values copied from cloned item.
		$( this ).parent().parent().children().filter('.gst-sortable').children().last().find('input').attr('value','');
		$( this ).parent().parent().children().filter('.gst-sortable').children().last().find('option:selected').removeAttr('selected');


		reInitProposalTypeList( e, $(this));

	});


	$(".option-tree-setting-body").append('<a class="ot_setting_body_done">Done</a>');
	
	getProposal( $("#meta_map_proposal_select").val() );
	

	$('#meta_map_proposal_select').change( function() {
		getProposal( this.value );
	});
	
	$(".ot_setting_body_done").click( function() {
		$('.option-tree-setting-body[style="display: block;"]').css('display','none');
	});
	
	$('.option-tree-list-item-add').click( function() {
		// console.log('got it');
		setTimeout( function() {
			 $('.option-tree-setting-body[style="display: block;"]').append('<a class="ot_setting_body_done">Done</a>');
			 $('.option-tree-setting-body[style="display: block;"] .ot_setting_body_done').click( function() {
					$('.option-tree-setting-body[style="display: block;"]').css('display','none');
				});
		}, 500);
	});

	function setPostEditOptions() {

		$.ajax({
			url:  cmp_admin_objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
			data:{
				'action':'crowdmap_ajax',
				'fn':'get_post_edit_options'
			},
			dataType: 'JSON',
			success:function(data){
				if ( data.errorData != null && data.errorData == 'true' ) {
					reportError( data );
				}

				// Set options here.
				var postEditOptions = data;
				$('.gst_countable').simplyCountable( {
					counter:            '#gst_counter',
					countType:          'characters',
					maxCount:           postEditOptions.map_excerpt_max_char_count,
					strictMax:          false,
					overClass:			'gst-excerpt-over',
					countDirection:     'down'
				});

			},
			error: function(errorThrown){
				console.log(errorThrown);
			}

		});


	}


	
	/**
	 * Get proposal functions.
	 */
	
	function getProposal( postID ) {
			
		if ( postID != null && postID != "" ) {
			$.ajax({
				url:  cmp_admin_objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
				data:{
					'action':'crowdmap_ajax',
					'fn':'get_proposal',
					'postID' : postID
				},
				dataType: 'JSON',
				success:function(data){
					if ( data.errorData != null && data.errorData == 'true' ) {
						reportError( data );
					}
					displayProposal( data );
			    },
				error: function(errorThrown){
					alert( cmp_admin_objectl10n.get_proposal_error + errorThrown.responseText.substring(0,500) );
					console.log(errorThrown);
			    }
			
			});
		}
		else {
			displayProposal("");
		}
	}





	function handleImageUpload( e, elem ) {

		e.preventDefault();


		// tag fields that will be updated by the media metabox.
		elem.siblings('.gst_image_url_target').addClass('gst-set-media-target');
		elem.siblings('a').children('img').addClass('gst-set-media-thumbnail-target');


		// Sets up the media library frame
		meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
			// title: meta_image.title,
			// button: { text:  meta_image.button },
			library: { type: 'image' }
		});

		// Runs when an image is selected.
		meta_image_frame.on('select', function(){


			// Grabs the attachment selection and creates a JSON representation of the model.
			var media_attachment = meta_image_frame.state().get('selection').first().toJSON();

			// Sends the attachment URL to our custom image input field.
			$('.gst-set-media-target').val(media_attachment.url);
			$('.gst-set-media-target').removeClass('gst-set-media-target');
			$('.gst-set-media-thumbnail-target').attr( "src", media_attachment.url );
			$('.gst-set-media-thumbnail-target').removeClass('gst-set-media-thumbnail-target');


		});

		// Opens the media library frame.
		// wp.media.editor.open();
		meta_image_frame.open();
	}


	function reInitProposalTypeList ( e, elem ) {

		$('#meta_proposal_type_list').children().each( function( idx, elem ) {
			var liHTML = $( elem ).html();
			var replaceStr = 'meta_proposal_type_list[' + idx + ']' ;
			var modliHTML = liHTML.replace(/meta_proposal_type_list\[[0-9]*\]/g, replaceStr );
			modliHTML = '<li class="ui-state-default">' +  modliHTML + '</li>';
			$( this).replaceWith( modliHTML);
		});


        $('#meta_proposal_type_list li:last .gst-color-input').wpColorPicker();

		$('.gst_proposal_type_input').on('change', function() {
			$(this).attr("value", $(this).val() );
		});


		$('#meta_proposal_type_list .gst-sort-edit-delete').click( function() {
			$(this).parent().parent().remove();

			$('.image-picker').each( function(idx, elem) {
				$( elem ).data('picker').destroy();
			});

			reInitProposalTypeList( e, $(this) );
		});

	}

	function getProposalsRanked( mapID ) {

        showSpinner();

		if ( mapID != null && mapID != "" ) {
			$.ajax({
				url:  cmp_admin_objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
				data:{
					'action':'crowdmap_ajax',
					'fn':'get_proposals_ranked',
					'mapID' : mapID
				},
				dataType: 'JSON',
				success:function(data){
					if ( data.errorData != null && data.errorData == 'true' ) {
						reportError( data );
					}
                    stopSpinner();
					displayProposalTable( data );
				},
				error: function(errorThrown){
                    stopSpinner();
					console.log( cmp_admin_objectl10n.get_proposal_error + errorThrown.responseText.substring(0,500) );
				}

			});
		}
		else {
			displayProposal("");
		}

	}
	function displayProposalTable( proposals ) {

        var tableHeaderString = cmp_admin_objectl10n.proposal_table_heading_template;

        var tableRowString = '';

		for( var i = 0 ; i < proposals.length ; i++ ) {
            tableRowString += cmp_admin_objectl10n.proposal_table_row_template.replace('__proposal.id__', proposals[i].id );
            tableRowString = tableRowString.replace('__proposal.title__', proposals[i].title );
            tableRowString = tableRowString.replace('__proposal.proposal_type__', proposals[i].proposal_type );
            tableRowString = tableRowString.replace('__proposal.supportCount__', proposals[i].support_count );
            tableRowString = tableRowString.replace('__proposal.lat__', proposals[i].lat );
            tableRowString = tableRowString.replace('__proposal.lng__', proposals[i].lng );
            tableRowString = tableRowString.replace('__proposal.permalink__', proposals[i].permalink );
		}

        tableRowString = tableHeaderString + tableRowString;

        var tableString = cmp_admin_objectl10n.proposal_table_template.replace('__proposal.tableRows__', tableRowString );

        jQuery('.cmp-report-output').html( tableString ) ;

	}

    function displayProposal() {

    }


	function testFunction( e, elem ) {
		alert('test function');
	}

	function reportError ( error ) {
		console.log ( error );
	}


	
});
