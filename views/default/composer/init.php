<?php 
$owner_guid = elgg_get_page_owner_guid();
$permission = true;
elgg_require_js('elgg/composer/init.composer');

$container = get_entity($owner_guid);
if(elgg_instanceof($container, 'group')){
	if($container->guid != $corporation_id)
		$permission = false;	
}

if (elgg_is_admin_logged_in()) 
	$permission = true;
?>

<?php if ($permission): ?>	

	<div id="composer">
		<div id="composer_tool_bar">	
			<div class="composer_msj_tools"><?php echo elgg_echo('thewire') ?></div>		
			<div class="composer_tools">                       
				<a id="composer_blog" title="<?php echo elgg_echo('blog:blog') ?>"><i class="fa fa-file-text fa-lg"></i></a>&nbsp;&nbsp;
				<a id="composer_bookmark" title="<?php echo elgg_echo('bookmarks:bookmark') ?>"><i class="fa fa-link fa-lg"></i></a>&nbsp;&nbsp;
				<a id="composer_file" title="<?php echo elgg_echo('file:file') ?>"><i class="fa fa-paperclip fa-lg"></i></a>			
			</div>
			<div class="close_tool" style="display:none"></div>                   
		</div>
		<form id="composer_form" class="elgg-form-file-upload" enctype="multipart/form-data" action="<?php echo elgg_get_site_url() ?>action/file/upload" method="post">
			<?php echo elgg_view('input/securitytoken');?>
			
			<input type="hidden" id="composer_container_guid" name="container_guid" value="<?php echo $owner_guid ?>">
			
			<input type="hidden" id="composer_type_content" value="wire">
			
			<div class="input_text elgg-form-small" id="input_title">
				<input type="text" name="title" id="composer_title" placeholder="<?php echo elgg_echo('title') ?>" style="display:none">
			</div>
			<div class="input_textarea  elgg-form-small" id="input_address">
				<input class="elgg-input-text" type="text" name="address" id="composer_address" placeholder="<?php echo elgg_echo('address') ?>" style="display:none">
			</div>
			<div class="input_textarea  elgg-form-small" id="input_body">
				<textarea name="description" id="composer_description" class="elgg-input-longtext" cols="30" rows="2" placeholder="<?php echo elgg_echo('composer:description') ?>" data-max-length="140"></textarea>
			</div>

			<div class="input_textarea  elgg-form-small" id="input_body">
				<input type="file" name="upload" id="composer_upload" style="display:none"/>
			</div>
			<?php echo elgg_view('input/access', array('name' => 'access_id', 'value' => '1', 'internalid'=>'composer_access_id')); ?>

			<div class="composer_info">
				<label id="loading-percent"></label>
				<span id="characters-remaining"></span>			
			</div>
		</form>
		<div>			
			<input id="composer_save" class="elgg-button elgg-button-submit" type="submit" value="<?php echo elgg_echo('save') ?>">
		</div>
	</div>
<?php endif ?>