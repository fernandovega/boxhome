define(function(require) {
	var $ = require('jquery');
	require('jquery.form');
	var elgg = require('elgg');

	var save_button = $("#composer_save");
	var type_content = $("#composer_type_content");
  var maxLength = 140;

  var init = function() {

		$("#composer_save").click(function(){
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

		$("#composer_blog").click(function(){
	    	hideComposerForm();
	    	$(".composer_msj_tools").html(elgg.echo('blog:blog'));
	    	type_content.val('blog');
	    	$("#composer_title").show();
	    	$("#characters-remaining").hide();
		});

		$("#composer_bookmark").click(function(){
	    	hideComposerForm();
	    	$(".composer_msj_tools").html(elgg.echo('bookmarks:bookmark'));
	    	type_content.val('bookmark');
	    	$("#composer_title").show();    	
	    	$("#composer_address").show();
	    	$("#composer_address").attr('placeholder', elgg.echo('bookmarks:url'));
	    	$("#characters-remaining").hide();
		});

	
		$("#composer_file").click(function(){
	    	hideComposerForm();
	    	$(".composer_msj_tools").html(elgg.echo('file:file'));
	    	type_content.val('file');
	    	$("#composer_title").show();
	    	$("#composer_upload").show();
	    	$("#characters-remaining").hide();
		});

		$(".close_tool").click(function(){
	    	hideComposerForm();
	    	$(".composer_msj_tools").html(elgg.echo('thewire'));
	    	type_content.val('wire');
	    	$(".close_tool").hide();
	    	$(".composer_tools").show();
	    	$("#characters-remaining").show();
	    	textCounter($("#composer_description"), $("#characters-remaining"), maxLength);
		});

		var callback = function() {
			if(type_content.val() =='wire'){				
				if (maxLength) {
					textCounter(this, $("#characters-remaining"), maxLength);
				}				
			}
		};

		$("#composer_description").live({
			input: callback,
			onpropertychange: callback
		});

	};

	elgg.register_hook_handler('init', 'system', init);
	

	var updateRiver = function(e) {
		$('#loading-percent').html("updating...");
		elgg.get(
			'composer/activity', 
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
					body: $("#composer_description").val(),
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
					title: $("#composer_title").val(),
					description: $("#composer_description").val(),
					access_id: $("#composer_access_id").val(),
					container_guid:  $("#composer_container_guid").val(),
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
					title: $("#composer_title").val(),
					description: $("#composer_description").val(),
					address: $("#composer_address").val(),
					access_id: $("#composer_access_id").val(),
					container_guid: $("#composer_container_guid").val(),
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
		$("#composer_form").ajaxForm({
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
		$("#composer_save").show();
		$("#composer_title").val("");
		$("#composer_description").val("");
		$("#composer_address").val("");
		$("#composer_upload").val("");
		$("#composer_title").hide();
		$("#composer_address").hide();
		$("#composer_upload").hide();
		$(".close_tool").hide();
		$(".composer_msj_tools").html(elgg.echo('thewire'));
  	type_content.val('wire');
		$(".composer_tools").show();
		$('#loading-percent').html("");
		$("#characters-remaining").html("");
		$("#characters-remaining").show();
	}

	var hideComposerForm = function(e) {
		$(".composer_tools").hide();
		$(".close_tool").show();
		$("#composer_title").hide();
		$("#composer_address").hide();
		$("#composer_upload").hide();
		$("#composer_save").removeAttr('disabled', 'disabled');
		$("#composer_save").removeClass('elgg-state-disabled');
	}

	var textCounter = function(textarea, status, limit) {

			var remaining_chars = limit - $(textarea).val().length;
			status.html(remaining_chars + ' ' +elgg.echo('thewire:charleft'));

			if (remaining_chars < 0) {
				status.parent().addClass("thewire-characters-remaining-warning");
				$("#composer_save").attr('disabled', 'disabled');
				$("#composer_save").addClass('elgg-state-disabled');
			} else {
				$("#composer_save").removeAttr('disabled', 'disabled');
				$("#composer_save").removeClass('elgg-state-disabled');
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
