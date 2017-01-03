<?php
$owner_guid = elgg_get_page_owner_guid();
$permission = true;
elgg_require_js('elgg/boxhome/init.boxhome');

$container = get_entity($owner_guid);
if(elgg_instanceof($container, 'group')){
	if($container->guid != $corporation_id)
		$permission = false;
}

if (elgg_is_admin_logged_in())
	$permission = true;
?>

<?php if ($permission) { ?>

	<div id="boxhome">
		<div id="boxhome_tool_bar">
			<div class="boxhome_msj_tools"><?php echo elgg_echo('thewire') ?></div>
			<div class="boxhome_tools">
				<?php if (elgg_is_active_plugin('blog')) { ?><a id="boxhome_blog" title="<?php echo elgg_echo('blog:blog') ?>"><i class="fa fa-file-text fa-lg"></i></a>&nbsp;&nbsp;<?php } ?>
				<?php if (elgg_is_active_plugin('bookmarks')) { ?><a id="boxhome_bookmark" title="<?php echo elgg_echo('bookmarks:bookmark') ?>"><i class="fa fa-link fa-lg"></i></a>&nbsp;&nbsp;<?php } ?>
				<?php if (elgg_is_active_plugin('files')) { ?><a id="boxhome_file" title="<?php echo elgg_echo('file:file') ?>"><i class="fa fa-paperclip fa-lg"></i></a><?php } ?>
			</div>
			<div class="close_tool" style="display:none"></div>
		</div>
		<form id="boxhome_form" class="elgg-form-file-upload" enctype="multipart/form-data" action="<?php echo elgg_get_site_url() ?>action/file/upload" method="post">
			<?php echo elgg_view('input/securitytoken');?>

			<input type="hidden" id="boxhome_container_guid" name="container_guid" value="<?php echo $owner_guid ?>">

			<input type="hidden" id="boxhome_type_content" value="wire">

			<div class="input_text elgg-form-small" id="input_title">
				<input type="text" name="title" id="boxhome_title" placeholder="<?php echo elgg_echo('title') ?>" style="display:none">
			</div>
			<div class="input_textarea  elgg-form-small" id="input_address">
				<input class="elgg-input-text" type="text" name="address" id="boxhome_address" placeholder="<?php echo elgg_echo('address') ?>" style="display:none">
			</div>
			<div class="input_textarea  elgg-form-small" id="input_body">
				<textarea name="description" id="boxhome_description" class="elgg-input-longtext" cols="30" rows="2" placeholder="<?php echo elgg_echo('boxhome:description') ?>" data-max-length="140"></textarea>
			</div>

			<div class="input_textarea  elgg-form-small" id="input_body">
				<input type="file" name="upload" id="boxhome_upload" style="display:none"/>
			</div>
			<?php echo elgg_view('input/access', array('name' => 'access_id', 'value' => '1', 'internalid'=>'boxhome_access_id')); ?>

			<div class="boxhome_info">
				<label id="loading-percent"></label>
				<span id="characters-remaining"></span>
			</div>
		</form>
		<div>
			<input id="boxhome_save" class="elgg-button elgg-button-submit" type="submit" value="<?php echo elgg_echo('save') ?>">
		</div>
	</div>
<?php } ?>
