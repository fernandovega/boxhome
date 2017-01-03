define(function(require) {
	var $ = require('jquery');
	require('jquery.form');
	var elgg = require('elgg');

	var save_button = $("#boxhome_save");
	var type_content = $("#boxhome_type_content");
  var maxLength = 140;

  var init = function() {

		$("#boxhome_save").click(function(){
	    	save_button.attr('disabled','disabled');
	    	switch (type_content.val()) {
	    		case 'wire':
			      	 saveWire();
			      	 break ;
			   	case 'blog':
			      	 saveBlog();
			      	 break ;
			   	case 'bookmark':
			      	 saveBookmark();
			      	 break ;
			    case 'file':
			      	 saveFile();
			      	 break ;
			}

	    return false;
		});

		$("#boxhome_blog").click(function(){
	    	hideComposerForm();
	    	$(".boxhome_msj_tools").html(elgg.echo('blog:blog'));
	    	type_content.val('blog');
	    	$("#boxhome_title").show();
	    	$("#characters-remaining").hide();
		});

		$("#boxhome_bookmark").click(function(){
	    	hideComposerForm();
	    	$(".boxhome_msj_tools").html(elgg.echo('bookmarks:bookmark'));
	    	type_content.val('bookmark');
	    	$("#boxhome_title").show();
	    	$("#boxhome_address").show();
	    	$("#boxhome_address").attr('placeholder', elgg.echo('bookmarks:url'));
	    	$("#characters-remaining").hide();
		});


		$("#boxhome_file").click(function(){
	    	hideComposerForm();
	    	$(".boxhome_msj_tools").html(elgg.echo('file:file'));
	    	type_content.val('file');
	    	$("#boxhome_title").show();
	    	$("#boxhome_upload").show();
	    	$("#characters-remaining").hide();
		});

		$(".close_tool").click(function(){
	    	hideComposerForm();
	    	$(".boxhome_msj_tools").html(elgg.echo('thewire'));
	    	type_content.val('wire');
	    	$(".close_tool").hide();
	    	$(".boxhome_tools").show();
	    	$("#characters-remaining").show();
	    	textCounter($("#boxhome_description"), $("#characters-remaining"), maxLength);
		});

		var callback = function() {
			if(type_content.val() =='wire'){
				if (maxLength) {
					textCounter(this, $("#characters-remaining"), maxLength);
				}
			}
		};

		// $("#boxhome_description").live({
		// 	input: callback,
		// 	onpropertychange: callback
		// });

	};

	elgg.register_hook_handler('init', 'system', init);


	var updateRiver = function(e) {
		$('#loading-percent').html("updating...");
		elgg.get(
			'boxhome/activity',
			{
					success: function(resultText, success, xhr) {
						resetComposerForm();
						$('#loading-percent').html("");
						$(".elgg-list-river").prepend(resultText);
					}
			}
		);
	}

	var saveWire = function(e) {
		elgg.action(
			"thewire/add",
			{
				data:{
					body: $("#boxhome_description").val(),
					__elgg_ts: elgg.security.token.__elgg_ts,
	   			__elgg_token: elgg.security.token.__elgg_token
				},
				success: function(r){
					if(r.status==0){
						elgg.system_message(r.message);
						updateRiver();

					}else{
						save_button.removeAttr('disabled');
						elgg.register_error(r.message);
					}
				}
			}
		);
	}

	var saveBlog = function(e) {

		elgg.action(
			"blog/save",
			{
				data:{
					title: $("#boxhome_title").val(),
					description: $("#boxhome_description").val(),
					access_id: $("#boxhome_access_id").val(),
					container_guid:  $("#boxhome_container_guid").val(),
					status: 'published',
					comments_on: 'On',
					save: 'Guardar',
					__elgg_ts: elgg.security.token.__elgg_ts,
	   			__elgg_token: elgg.security.token.__elgg_token
				},
				success: function(r){
					if(r.status==0){
						elgg.system_message(r.message);
						updateRiver();

					}else{
						save_button.removeAttr('disabled');
						elgg.register_error(r.message);
					}
				}
			}
		);
	}

	var saveBookmark = function(e) {

		elgg.action(
			"bookmarks/save",
			{
				data:{
					title: $("#boxhome_title").val(),
					description: $("#boxhome_description").val(),
					address: $("#boxhome_address").val(),
					access_id: $("#boxhome_access_id").val(),
					container_guid: $("#boxhome_container_guid").val(),
					__elgg_ts: elgg.security.token.__elgg_ts,
	   			__elgg_token: elgg.security.token.__elgg_token
				},
				success: function(r){
					if(r.status==0){
						elgg.system_message(r.message);
						updateRiver();

					}else{
						save_button.removeAttr('disabled');
						elgg.register_error(r.message);
					}
				}
			}
		);
	}

	var saveFile = function(e) {
	// Save File depent of plugin jquery.form
		$("#boxhome_form").ajaxForm({
	            dataType:  'json',
	            //clearForm: true,
	            uploadProgress: function(event, position, total, percentComplete) {
					        var percentVal = percentComplete + '%';
					        $('#loading-percent').html('Uploading '+percentVal);
					    },
	            success:   saveFileResultJson
	        }).submit();
	}

	var saveFileResultJson = function(r) {
	    if(r.status==0){
				elgg.system_message(r.system_messages.success[0]);
				updateRiver();
		  }else{
			  $('#loading-percent').html("");
			  save_button.removeAttr('disabled');
				elgg.register_error(r.system_messages.error[0]);
		  }
	}

	var resetComposerForm = function(e) {
		save_button.removeAttr('disabled');
		$("#boxhome_save").show();
		$("#boxhome_title").val("");
		$("#boxhome_description").val("");
		$("#boxhome_address").val("");
		$("#boxhome_upload").val("");
		$("#boxhome_title").hide();
		$("#boxhome_address").hide();
		$("#boxhome_upload").hide();
		$(".close_tool").hide();
		$(".boxhome_msj_tools").html(elgg.echo('thewire'));
  	type_content.val('wire');
		$(".boxhome_tools").show();
		$('#loading-percent').html("");
		$("#characters-remaining").html("");
		$("#characters-remaining").show();
	}

	var hideComposerForm = function(e) {
		$(".boxhome_tools").hide();
		$(".close_tool").show();
		$("#boxhome_title").hide();
		$("#boxhome_address").hide();
		$("#boxhome_upload").hide();
		$("#boxhome_save").removeAttr('disabled', 'disabled');
		$("#boxhome_save").removeClass('elgg-state-disabled');
	}

	var textCounter = function(textarea, status, limit) {

			var remaining_chars = limit - $(textarea).val().length;
			status.html(remaining_chars + ' ' +elgg.echo('thewire:charleft'));

			if (remaining_chars < 0) {
				status.parent().addClass("thewire-characters-remaining-warning");
				$("#boxhome_save").attr('disabled', 'disabled');
				$("#boxhome_save").addClass('elgg-state-disabled');
			} else {
				$("#boxhome_save").removeAttr('disabled', 'disabled');
				$("#boxhome_save").removeClass('elgg-state-disabled');
				status.parent().removeClass("thewire-characters-remaining-warning");
			}
	};

	return {
		updateRiver: updateRiver,
		saveWire: saveWire,
		saveBlog: saveBlog,
		saveBookmark: saveBookmark,
		saveFile: saveFile,
		saveFileResultJson: saveFileResultJson,
		resetComposerForm: resetComposerForm,
		hideComposerForm: hideComposerForm,
	};

});
